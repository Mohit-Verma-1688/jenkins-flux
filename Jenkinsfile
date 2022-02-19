dockerRepoHost = 'docker.io'
dockerRepoUser = 'mohitverma1688' // (Username must match the value in jenkinsDockerSecret)
dockerRepoProj = 'php-app'

// these refer to a Jenkins secret "id", which can be in Jenkins global scope:
jenkinsDockerSecret = 'docker-registry-account1'

// blank values that are filled in by pipeline steps below:
gitCommit = ''
branchName = ''
unixTime = ''
developmentTag = ''
releaseTag = ''

pipeline {
  environment {
        // To get the tag like shown soru's answer:
         TAG_NAME = sh(returnStdout: true, script: "git describe --tags").trim()

        // In my case I already have a tag saved as an environment variable:
        // gitlabBranch=refs/tags/tagname
       // TAG_NAME = "${env.gitlabBranch.split('/')[2]}"
    }
  agent {
    kubernetes { yamlFile "jenkins/docker-pod.yaml" }
  }
    }
  stages {
    // Build a Docker image and keep it locally for now
    stage('Build') {
      steps {
        container('docker') {
          script {
            gitCommit = env.GIT_COMMIT.substring(0,8)
            branchName = env.BRANCH_NAME
            unixTime = (new Date().time / 1000) as Integer
            developmentTag = "${branchName}-${gitCommit}-${unixTime}"
            developmentImage = "${dockerRepoUser}/${dockerRepoProj}:${developmentTag}"
          }
          sh "docker build -t ${developmentImage} ./"
        }
      }
    }
    // Push the image to development environment, and run tests in parallel
    stage('Dev') {
      parallel {
        stage('Push Development Tag') {
          when {
            not {
              buildingTag()
            }
          }
          steps {
            withCredentials([[$class: 'UsernamePasswordMultiBinding',
              credentialsId: jenkinsDockerSecret,
              usernameVariable: 'DOCKER_REPO_USER',
              passwordVariable: 'DOCKER_REPO_PASSWORD']]) {
              container('docker') {
                sh """\
                  docker login -u \$DOCKER_REPO_USER -p \$DOCKER_REPO_PASSWORD
                  docker push ${developmentImage}
                """.stripIndent()
              }
            }
          }
        }
        // Start a second agent to create a pod with the newly built image
        stage('Test') {
          agent {
            kubernetes {
              yaml """\
                apiVersion: v1
                kind: Pod
                spec:
                  containers:
                  - name: test
                    image: ${developmentImage}
                    imagePullPolicy: Never
                    securityContext:
                      runAsUser: 1000
                    command:
                    - cat
                    resources:
                      requests:
                        memory: 100Mi
                        cpu: 30m
                      limits:
                        memory: 300Mi
                        cpu: 100m
                    tty: true
                """.stripIndent()
            }
          }
          options { skipDefaultCheckout(true) }
          steps {
            // Run the tests in the new test container
            container('test') {
              echo 'testing stage running'
            }
          }
        }
      }
    }
    stage('Push Release Tag') {
      when {
        buildingTag()
      }
      steps {
        script {
          releaseTag = env.TAG_NAME
          releaseImage = "${dockerRepoUser}/${dockerRepoProj}:${releaseTag}"
        }
        container('docker') {
          withCredentials([[$class: 'UsernamePasswordMultiBinding',
            credentialsId: jenkinsDockerSecret,
            usernameVariable: 'DOCKER_REPO_USER',
            passwordVariable: 'DOCKER_REPO_PASSWORD']]) {
            sh """\
              docker login -u \$DOCKER_REPO_USER -p \$DOCKER_REPO_PASSWORD
              docker tag ${developmentImage} ${releaseImage}
              docker push ${releaseImage}
            """.stripIndent()
          }
        }
      }
    }
  }
}
