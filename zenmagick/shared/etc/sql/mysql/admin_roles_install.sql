DROP TABLE IF EXISTS admin_roles;
CREATE TABLE admin_roles (
  admin_role_id int(11) NOT NULL auto_increment,
  name varchar(32) NOT NULL,
  PRIMARY KEY (admin_role_id),
  UNIQUE (name)
) TYPE=MyISAM;

DROP TABLE IF EXISTS admins_to_roles;
CREATE TABLE admins_to_roles (
  admin_id int(11),
  admin_role_id int(11),
  PRIMARY KEY (admin_id, admin_role_id),
  FOREIGN KEY (admin_role_id) REFERENCES admin_roles (admin_role_id) ON DELETE CASCADE,
  FOREIGN KEY (admin_id) REFERENCES admin (admin_id) ON DELETE CASCADE
) TYPE=MyISAM;

## create default mapping for main admin
INSERT INTO admin_roles VALUES(1, 'admin');
INSERT INTO admin_roles VALUES(2, 'demo');

## create demo user with empty password, so it can't be used without manual change
INSERT INTO admin VALUES (2, 'Demo', 'demo@localhost', '7f20db8a9ea16394abbe87a99a359e7e:d4', 0);

INSERT INTO admins_to_roles VALUES(1, 1);
INSERT INTO admins_to_roles VALUES(2, 2);
