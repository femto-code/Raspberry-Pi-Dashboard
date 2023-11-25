Docker compose for Raspberry-Pi-Dashboard

Requirements:
  - Docker version 24.0.6 (tested. Maybe any version). Install docker https://docs.docker.com/engine/install/debian/
  - Docker compose plugin
  - If you build image on another architecture you need 'docker buildx'

Build and Start:
  - docker compose build --pull
  - docker compose up -d

Build for another architecture:
  - docker buildx create --name arm64
  - docker compose build --builder arm64 --pull
  - If you have container registry. Push 'docker compose push'