FROM mysql/mysql-server:8.0
LABEL maintainer="ucan-lab <yes@u-can.pro>"

# cnf側でJSTにする
ENV TZ=UTC

COPY ./infra/dev/mysql/my.cnf /etc/my.cnf
