CREATE DATABASE IF NOT EXISTS mi_api_rest_symfony;
USE mi_api_rest_symfony;

CREATE TABLE user(
    id       bigint(255) AUTO_INCREMENT NOT NULL,
    name     varchar(150) NOT NULL,
    lastname varchar(255),
    email    varchar(255) NOT NULL,
    password varchar(255) NOT NULL,
    role     varchar(255),
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id)
)ENGINE=InnoDb AUTO_INCREMENT=1;

CREATE TABLE  video(
    id       bigint(255) AUTO_INCREMENT NOT NULL,
    user_id  bigint(255) NOT NULL,
    title    varchar(255) NOT NULL,
    description  text,
    url    varchar(255) NOT NULL,
    status varchar(50),
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id),
   
    CONSTRAINT fk_video_user FOREIGN KEY(user_id) REFERENCES  user(id)
)ENGINE=InnoDb AUTO_INCREMENT=1;

