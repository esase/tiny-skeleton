Project installing 
------------------

1. Install the `docker` locally - https://docs.docker.com/install/
1. Clone this repository to somewhere locally - `git clone -b test https://github.com/esase/tiny-skeleton.git`

Docker Running
--------------

1. For launching the `docker` containers you need to run the command: `docker-compose up -d`
1. For stopping running containers you need to run the command: `docker-compose stop`


First time running
-------------------
1. To import a database dump, call the command: `docker exec -i tiny-skeleton-mysql mysql -uroot -ptiny-skeleton-root tiny-skeleton-db < docker-config/mysql/dump.sql`
1. To install php libraries call the command: `docker exec -i tiny-skeleton-web composer install --no-dev`

Tools
-----

1. The website is available on address: **http://localhost:8080** or **http://your.ip:8080**
1. Phpmyadmin is available on address: **http://localhost:8000** or **http://your.ip:8000**
