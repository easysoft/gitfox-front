English | [中文](Readme_zh.md)

<p align="center"><a href="https://www.gitfox.net" target="_blank" rel="noopener noreferrer"><img src="doc/img/gitfox.svg" width="300" alt="GitFox Logo"></a></p>

<p align="center">
  <a href="./LICENSE.EN"><img src="https://img.shields.io/badge/License-ZPL%201.2%20%2F%20AGPL%203.0-blue" alt="GitFox License"></a>
  <a href="https://www.gitfox.net"><img src="https://img.shields.io/badge/Website-www.gitfox.net-blue" alt="GitFox Website"></a>
</p>

# Introduction

GitFox Front is the web frontend of GitFox, an all-in-one code hosting and DevOps platform. Built on the ZenTao PHP MVC framework, it provides a unified web UI for product management, project management, requirements, tasks, bugs, testing, and integrates with the GitFox backend for Git repository hosting, code review, CI/CD pipelines, artifact repositories, and code scanning.

This repository contains the frontend only. The GitFox backend is maintained in [gitfox-server](https://github.com/easysoft/gitfox-server).

## Features

- **Product and project management**: products, projects, executions, requirements (stories), tasks, bugs, test cases, builds, and releases
- **Code hosting**: repositories, branches, tags, commits, merge requests, and webhooks
- **Code review**: merge request review flows, branch protection rules, and code owners
- **CI/CD**: pipeline management, runners, and scheduled jobs
- **Artifact repository**: container images, Helm charts, raw artifacts, recycle bin, and garbage collection
- **Code scanning**: rules, rule sets, scan plans, and scan tasks
- **Spaces and permissions**: multiple spaces, RBAC, and SSO/token authentication
- **GitFox integration**: user binding, issue association, and webhook integration with ZenTao
- **Metrics and reports**: built-in dashboards, metrics, and reports
- **Internationalization**: zh-CN, zh-TW, en, de, and fr

## Tech Stack

- PHP >= 5.6 (PHP 8.1+ recommended)
- ZenTaoPHP MVC framework
- ZUI and jQuery
- MySQL / MariaDB
- Caddy (default web server in the container image)
- RoadRunner (background cron scheduler and consumer)
- Docker / Docker Compose

## Quick Start

### 1. Deploy with Docker Compose

Docker and Docker Compose are required. The example below starts three services:

- `zentao-db`: MySQL database
- `zentao`: GitFox frontend (`hub.qc.oop.cc/test/gitfox-front:4.6`)
- `gitfox`: GitFox backend

Create a `docker-compose.yaml` file:

```yaml
services:
  zentao-db:
    image: hub.zentao.net/app/mysql:8.0.36-debian
    container_name: zentao-db
    ports:
      - '3306:3306'
    volumes:
      - /data/zentao-mysql:/var/lib/mysql
    environment:
      - MYSQL_ROOT_PASSWORD=pass4Zentao
      - MYSQL_DATABASE=zentao
    networks:
      - zentao-net
    healthcheck:
      test: ["CMD-SHELL", "mysqladmin ping -h127.0.0.1 -uroot -p$$MYSQL_ROOT_PASSWORD"]
      interval: 3s
      timeout: 3s
      retries: 10
      start_period: 20s

  zentao:
    image: hub.qc.oop.cc/test/gitfox-front:4.5
    container_name: zentao
    ports:
      - '80:80'
    volumes:
      - /data/zentao-file:/data
    depends_on:
      zentao-db:
        condition: service_healthy
    environment:
      - ZT_MYSQL_HOST=zentao-db
      - ZT_MYSQL_PORT=3306
      - ZT_MYSQL_USER=root
      - ZT_MYSQL_PASSWORD=pass4Zentao
      - ZT_MYSQL_DB=zentao
      - PHP_MAX_EXECUTION_TIME=120
      - PHP_MEMORY_LIMIT=512M
      - PHP_POST_MAX_SIZE=128M
      - PHP_UPLOAD_MAX_FILESIZE=128M
      - ZT_GITFOX_PORT=3000
      - ZT_GITFOX_URL=3000
      - IS_CONTAINER=true
    networks:
      - zentao-net

  gitfox:
    image: hub.zentao.net/test/gitfox-pro:4.5
    container_name: gitfox
    ports:
      - '3000:3000'
      - '3322:3322'
    volumes:
      - /data/zentao-file:/data
      - /var/run/docker.sock:/var/run/docker.sock
    depends_on:
      zentao-db:
        condition: service_healthy
    networks:
      - zentao-net

networks:
  zentao-net:
    driver: bridge
```

Start the stack:

```bash
docker compose up -d
```

Then visit `http://<server-address>` to finish the initialization.

Before deployment, update the following according to your environment:

- MySQL password, data volume paths, and image addresses

> The example images point to internal registries. Replace them with accessible image addresses for public deployment, and do not use the example password in production.

### 2. Configuration

Frontend environment variables:

| Variable | Description |
| --- | --- |
| `ZT_MYSQL_HOST` | MySQL host |
| `ZT_MYSQL_PORT` | MySQL port |
| `ZT_MYSQL_USER` | MySQL user |
| `ZT_MYSQL_PASSWORD` | MySQL password |
| `ZT_MYSQL_DB` | MySQL database name |
| `ZT_GITFOX_PORT` | GitFox backend port |
| `ZT_GITFOX_URL` | GitFox backend URL or port |
| `PHP_MAX_EXECUTION_TIME` | PHP maximum execution time |
| `PHP_MEMORY_LIMIT` | PHP memory limit |
| `PHP_POST_MAX_SIZE` | PHP maximum POST size |
| `PHP_UPLOAD_MAX_FILESIZE` | PHP maximum upload file size |
| `IS_CONTAINER` | Set to `true` when running in a container |

### 3. Build from Source

Requirements: PHP >= 5.6 (PHP 8.1+ recommended), MySQL/MariaDB, and Caddy or Apache.

```bash
make common   # assemble the runnable source tree into zentaopms/
make pms      # build the distribution zip package
make ci       # build the full CI package
```

Build frontend assets:

```bash
php misc/minifyfront.php
```

Run the CLI tool:

```bash
php bin/ztcli
```

### 4. Development and Testing

```bash
cd test
php spider.php
```

Project layout:

- `framework/`: core framework classes
- `module/`: business modules
- `lib/`: third-party libraries and utilities
- `config/`: global configuration
- `db/`: database schemas and migrations
- `www/`: web entry point and public assets
- `bin/`: CLI tools
- `api/`: API definitions
- `roadrunner/`: background workers
- `test/`: testing framework and cases

## Container Images

| Component | Image |
| --- | --- |
| Frontend | `hub.qc.oop.cc/test/gitfox-front:4.5` |
| Backend | `hub.zentao.net/test/gitfox-pro:4.5` (example) |
| Database | `hub.zentao.net/app/mysql:8.0.36-debian` |

> The backend and database images above are examples and point to internal registries. Replace them with the versions and addresses available in your environment.

## License

This project is dual-licensed under **ZPL 1.2** and **AGPL 3.0**. You may choose either license. See [LICENSE.EN](./LICENSE.EN) and [GITFOX_LICENSE](./GITFOX_LICENSE) for details.

Copyright 2009-2026 Qucheng Geek Software (Qingdao) Co., Ltd. (www.gitfox.net)

## Contributing

Issues and Pull Requests are welcome.

## Links

- Website: [https://www.gitfox.net](https://www.gitfox.net)
- Manual: [https://gitfox.net/book/open-source/40.html](https://gitfox.net/book/open-source/40.html)
- Backend project: [https://github.com/easysoft/gitfox-server](https://github.com/easysoft/gitfox-server)
