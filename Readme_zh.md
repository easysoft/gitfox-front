[English](README.md) | 中文

<p align="center"><a href="https://www.gitfox.net" target="_blank" rel="noopener noreferrer"><img src="doc/img/gitfox.svg" width="300" alt="GitFox Logo"></a></p>

<p align="center">
  <a href="./LICENSE.CN"><img src="https://img.shields.io/badge/License-ZPL%201.2%20%2F%20AGPL%203.0-blue" alt="GitFox License"></a>
  <a href="https://www.gitfox.net"><img src="https://img.shields.io/badge/Website-www.gitfox.net-blue" alt="GitFox Website"></a>
</p>

# 介绍

GitFox 前端是 GitFox 一体化代码托管与 DevOps 平台的 Web 管理界面。它基于禅道的 PHP MVC 框架构建，统一提供产品管理、项目管理、需求、任务、缺陷、测试等功能，并与 GitFox 后端集成，覆盖 Git 仓库托管、代码评审、CI/CD 流水线、制品库和代码扫描等能力。

本仓库仅包含前端部分，GitFox 后端项目请参考 [gitfox-server](https://github.com/easysoft/gitfox-server)。

## 功能特性

- **产品与项目管理**：产品、项目、执行、需求（故事）、任务、缺陷、测试用例、版本与发布
- **代码托管**：仓库、分支、标签、提交、合并请求（Merge Request）与 Webhook
- **代码评审**：合并请求评审流程、分支保护规则与 Code Owner
- **CI/CD**：流水线管理、Runner 与定时任务
- **制品库**：容器镜像、Helm、Raw 等制品存储，以及回收站和垃圾回收
- **代码扫描**：规则、规则集、扫描方案与扫描任务
- **空间与权限**：多空间、RBAC 权限与 SSO/Token 认证
- **GitFox 集成**：用户绑定、Issue 关联，以及与禅道的 Webhook 集成
- **度量与报表**：内置仪表盘、度量项与报表
- **国际化**：简体中文、繁体中文、英文、德文与法文

## 技术栈

- PHP >= 5.6（推荐 PHP 8.1+）
- ZenTaoPHP MVC 框架
- ZUI 与 jQuery
- MySQL / MariaDB
- Caddy（容器镜像中的默认 Web 服务器）
- RoadRunner（后台定时任务调度与消费）
- Docker / Docker Compose

## 快速开始

### 1. 使用 Docker Compose 部署

需要安装 Docker 与 Docker Compose。下面的示例会启动三个服务：

- `zentao-db`：MySQL 数据库
- `zentao`：GitFox 前端（`hub.qc.oop.cc/test/gitfox-front:4.6`）
- `gitfox`：GitFox 后端

创建 `docker-compose.yaml` 文件：

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

启动服务：

```bash
docker compose up -d
```

启动后访问 `http://<服务器地址>` 完成初始化。

部署前请按实际情况修改：

- MySQL 密码、数据卷路径与镜像地址

> 示例中的镜像默认指向内部仓库，公开部署时请替换为可访问的镜像地址，并勿在生产环境使用示例密码。

### 2. 配置说明

前端环境变量：

| 变量 | 说明 |
| --- | --- |
| `ZT_MYSQL_HOST` | MySQL 主机地址 |
| `ZT_MYSQL_PORT` | MySQL 端口 |
| `ZT_MYSQL_USER` | MySQL 用户名 |
| `ZT_MYSQL_PASSWORD` | MySQL 密码 |
| `ZT_MYSQL_DB` | MySQL 数据库名称 |
| `ZT_GITFOX_PORT` | GitFox 后端端口 |
| `ZT_GITFOX_URL` | GitFox 后端地址或端口 |
| `PHP_MAX_EXECUTION_TIME` | PHP 最大执行时间 |
| `PHP_MEMORY_LIMIT` | PHP 内存限制 |
| `PHP_POST_MAX_SIZE` | PHP 最大 POST 大小 |
| `PHP_UPLOAD_MAX_FILESIZE` | PHP 最大上传文件大小 |
| `IS_CONTAINER` | 容器运行时设置为 `true` |

### 3. 从源码构建

环境要求：PHP >= 5.6（推荐 PHP 8.1+）、MySQL/MariaDB，以及 Caddy 或 Apache。

```bash
make common   # 组装可运行的源码目录到 zentaopms/
make pms      # 构建分发包 zip
make ci       # 构建完整 CI 包
```

构建前端资源：

```bash
php misc/minifyfront.php
```

运行命令行工具：

```bash
php bin/ztcli
```

### 4. 开发与测试

```bash
cd test
php spider.php
```

目录结构：

- `framework/`：核心框架类
- `module/`：业务模块
- `lib/`：第三方库与工具类
- `config/`：全局配置
- `db/`：数据库结构与迁移脚本
- `www/`：Web 入口与公共资源
- `bin/`：命令行工具
- `api/`：API 定义
- `roadrunner/`：后台任务进程
- `test/`：测试框架与用例

## 容器镜像

| 组件 | 镜像 |
| --- | --- |
| 前端 | `hub.qc.oop.cc/test/gitfox-front:4.5`（示例） |
| 后端 | `hub.zentao.net/test/gitfox-pro:4.5`（示例） |
| 数据库 | `hub.zentao.net/app/mysql:8.0.36-debian` |

> 上表中的后端与数据库镜像仅为示例，且默认指向内部仓库。请替换为你所在环境中可用的版本与地址。

## 许可证

本项目采用 **ZPL 1.2** 与 **AGPL 3.0** 双许可，你可任选其一。详见 [LICENSE.CN](./LICENSE.CN) 与 [GITFOX_LICENSE](./GITFOX_LICENSE)。

Copyright 2009-2026 渠成极客软件（青岛）有限公司（www.gitfox.net）

## 贡献

欢迎提交 Issue 与 Pull Request。

## 相关链接

- 官网：[https://www.gitfox.net](https://www.gitfox.net)
- 手册：[https://gitfox.net/book/open-source/40.html](https://gitfox.net/book/open-source/40.html)
- 后端项目：[https://github.com/easysoft/gitfox-server](https://github.com/easysoft/gitfox-server)
