# Resume_PHP

Requirements:
PHP >= 5.3
MySQL 3.x/4.x/5.x

You must configure the following to completely run the application:

Create a database (or use an existing one), for example misc, create the tables named user_id, Profile, Position Institution and Education , then add new users to user_id and new institutions to Institution tables with the following statements:

CREATE TABLE user_id (
	user_id INT PRIMARY KEY AUTO_INCREMENT,
	first_name VARCHAR (100) NOT NULL,
	last_name Varchar (100) NOT NUll,
	user_name VARCHAR (100) NOT NULL,
	user_password VARCHAR (255) NOT NULL,
	email VARCHAR (100) NOT NULL,
	verified tinyint(1),
	token VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Create TABLE Profile (
    profile_id INTEGER NOT NULL AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    first_name VARCHAR (100) NOT NULL,
    last_name VARCHAR (100) NOT NULL,
    email VARCHAR (100) NOT NULL,
    headline VARCHAR (150) NOT NULL,
    PRIMARY KEY(profile_id),
    FOREIGN KEY(user_id)
    REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE Position (
  position_id INTEGER NOT NULL AUTO_INCREMENT,
  profile_id INTEGER,
  rank INTEGER,
  year INTEGER,
  description TEXT,

  PRIMARY KEY(position_id),

  CONSTRAINT position_ibfk_1
        FOREIGN KEY (profile_id)
        REFERENCES Profile(profile_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE Institution (
  institution_id INTEGER NOT NULL KEY AUTO_INCREMENT,
  name VARCHAR(255),
  UNIQUE(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE Education (
  profile_id INTEGER,
  institution_id INTEGER,
  rank INTEGER,
  year INTEGER,
  CONSTRAINT education_ibfk_1
    FOREIGN KEY (profile_id)
    REFERENCES Profile (profile_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT education_ibfk_2
    FOREIGN KEY (institution_id)
    REFERENCES Institution (institution_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY(profile_id, institution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO users (name,email,password)
VALUES ('UMSI','umsi@umich.edu','1a52e17fa899cf40fb04cfc42e6352f1');
INSERT INTO users (name,email,password) 
VALUES ('Chuck','csev@umich.edu','1a52e17fa899cf40fb04cfc42e6352f1');

INSERT INTO Institution (name) VALUES ('University of Michigan');
INSERT INTO Institution (name) VALUES ('University of Virginia');
INSERT INTO Institution (name) VALUES ('University of Oxford');
INSERT INTO Institution (name) VALUES ('University of Cambridge');
INSERT INTO Institution (name) VALUES ('Stanford University');
INSERT INTO Institution (name) VALUES ('Duke University');
INSERT INTO Institution (name) VALUES ('Michigan State University');
INSERT INTO Institution (name) VALUES ('Mississippi State University');
INSERT INTO Institution (name) VALUES ('Montana State University');

Place the folder where you cloned this repo into your web server's root folder.
Configure the variables $HOST, $PORT, $DB_NAME, $DB_USER, $DB_PASSWORD in pdo.php file, accordnly to the values you configured in the previous step.
Now you can access to your folder's url in the browser, for example: localhost/res-education using any of this email for login: umsi@umich.edu or csev@umich.edu
