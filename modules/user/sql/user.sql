insert into user (private_nick) VALUES ('guest');
update user set IdUser=0 where private_nick='guest';
