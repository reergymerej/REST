Here's a stab at a RESTful PHP API
==================================

I'm so tired of getting distracted with database creation every time I have an idea I want to try out.  90% of the time I don't need to do complex queries, I just want to be able to throw whatever I want at a db and have it stick.

### sql setup

By the way, the username/password in the connection script is *NOT* real.

>CREATE TABLE `crud` (
>  `id` int(11) NOT NULL AUTO_INCREMENT,
>  `noun` varchar(45) NOT NULL,
>  `noun_id` int(11) DEFAULT NULL,
>  `field_name` varchar(45) NOT NULL,
>  `field_int` int(11) DEFAULT NULL,
>  `field_varchar` varchar(100) DEFAULT NULL,
>  `field_float` float DEFAULT NULL,
>  PRIMARY KEY (`id`)
>);