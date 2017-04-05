CREATE TABLE role_user(
  id int(1) NOT NULL,
  libelle varchar(32) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE utilisateur(
  id int(8) NOT NULL,
  email varchar(255) NOT NULL,
  nom varchar(32) NOT NULL,
  prenom varchar(32) NOT NULL,
  mdp varchar(32) NOT NULL,
  role int(1),
  site int(8),
  unite int(8),
  PRIMARY KEY(id),
  CONSTRAINT FK_ROLE_USER FOREIGN KEY(role) REFERENCES role_user(id),
  CONSTRAINT FK_SITE_MANAGER FOREIGN KEY(site) REFERENCES site(id),
  CONSTRAINT FK_UNITE_EXPERT FOREIGN KEY(unite) REFERENCES unite(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE permanence(
  id int(8) NOT NULL,
  date date NOT NULL
)

CREATE TABLE assurePermanence(
  expert_id int(8) NOT NULL,
  permanence_id int(8) NOT NULL,
  disponibilite varchar(32) NOT NULL,
  CHECK(disponibilite) IN (Matin, Après midi, Journée),
  PRIMARY KEY(expert_id, permanence_id),
  FOREIGN KEY(expert_id) REFERENCES user(id),
  FOREIGN KEY(permanence_id) REFERENCES permanence(id)
)

CREATE TABLE site(
  id int(8) NOT NULL,
  libelle varchar(32) NOT NULL,
  adresse varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE unite(
  id int(8) NOT NULL,
  libelle varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE theme(
  id int(4) NOT NULL,
  libelle varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE pj(
  id int(8) NOT NULL,
  path varchar(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE demande(
  id int(8) NOT NULL,
  objet varchar(255) NOT NULL,
  etat varchar(32) NOT NULL,
  estArchive boolean,
  manager int(8) NOT NULL,
  theme int(4) NOT NULL,
  pj int(8),
  PRIMARY KEY(id),
  FOREIGN KEY(manager) REFERENCES user(id),
  FOREIGN KEY(theme) REFERENCES theme(id),
  FOREIGN KEY(pj) REFERENCES pj(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE message(
  id int(8) NOT NULL,
  description varchar(1000) NOT NULL,
  date TIMESTAMP NOT NULL,
  auteur int(8) NOT NULL,
  demande int(8) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(auteur) REFERENCES user(id),
  FOREIGN KEY(demande) REFERENCES demande(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
