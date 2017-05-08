CREATE TABLE "tag" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text(20) NOT NULL
);

INSERT INTO "tag" ("id", "name") VALUES (1,	'popular');
INSERT INTO "tag" ("id", "name") VALUES (2,	'ebook');

CREATE TABLE "borrower" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL
);

INSERT INTO "borrower" ("id", "name") VALUES (1,	'Vojtech Kohout');
INSERT INTO "borrower" ("id", "name") VALUES (2,	'John Doe');
INSERT INTO "borrower" ("id", "name") VALUES (3,	'Jane Roe');

CREATE TABLE "author" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL,
  "web" text NULL
);

INSERT INTO "author" ("id", "name", "web") VALUES (1,	'Andrew Hunt',	NULL);
INSERT INTO "author" ("id", "name", "web") VALUES (2,	'Donald Knuth',	NULL);
INSERT INTO "author" ("id", "name", "web") VALUES (3,	'Martin Fowler',	'http://martinfowler.com');
INSERT INTO "author" ("id", "name", "web") VALUES (4,	'Kent Beck',	NULL);
INSERT INTO "author" ("id", "name", "web") VALUES (5,	'Thomas H. Cormen',	NULL);

CREATE TABLE "book" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "author_id" integer NOT NULL,
  "reviewer_id" integer NULL,
  "pubdate" text NOT NULL,
  "name" text NOT NULL,
  "description" text NULL,
  "website" text NULL,
  "available" integer NOT NULL DEFAULT '1',
  FOREIGN KEY ("author_id") REFERENCES "author" ("id") ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY ("reviewer_id") REFERENCES "author" ("id") ON DELETE SET NULL  ON UPDATE CASCADE
);

INSERT INTO "book" ("id", "author_id", "reviewer_id", "pubdate", "name", "description", "website", "available") VALUES (1,	1,	NULL,	'1999-10-30',	'The Pragmatic Programmer',	NULL,	'http://en.wikipedia.org/wiki/The_Pragmatic_Programmer',	1);
INSERT INTO "book" ("id", "author_id", "reviewer_id", "pubdate", "name", "description", "website", "available") VALUES (2,	2,	1,	'1968-04-08',	'The Art of Computer Programming',	'very old book about programming',	NULL,	'0');
INSERT INTO "book" ("id", "author_id", "reviewer_id", "pubdate", "name", "description", "website", "available") VALUES (3,	3,	4,	'1999-07-08',	'Refactoring: Improving the Design of Existing Code',	NULL,	'http://martinfowler.com/books/refactoring.html',	1);
INSERT INTO "book" ("id", "author_id", "reviewer_id", "pubdate", "name", "description", "website", "available") VALUES (4,	5,	NULL,	'2009-07-31',	'Introduction to Algorithms',	'The book covers a broad range of algorithms in depth, yet makes their design and analysis accessible to all levels of readers.',	NULL,	1);
INSERT INTO "book" ("id", "author_id", "reviewer_id", "pubdate", "name", "description", "website", "available") VALUES (5,	3,	NULL,	'2003-09-25',	'UML Distilled',	NULL,	'http://martinfowler.com/books/refactoring.html',	'0');

CREATE TABLE "book_tag" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "book_id" integer NOT NULL,
  "tag_id" integer NOT NULL,
  FOREIGN KEY ("book_id") REFERENCES "book" ("id") ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY ("tag_id") REFERENCES "tag" ("id") ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE UNIQUE INDEX "book_tag_book_id_tag_id" ON "book_tag" ("book_id", "tag_id");

INSERT INTO "book_tag" ("id", "book_id", "tag_id") VALUES (1,	1,	1);
INSERT INTO "book_tag" ("id", "book_id", "tag_id") VALUES (2,	1,	2);
INSERT INTO "book_tag" ("id", "book_id", "tag_id") VALUES (3,	3,	2);
INSERT INTO "book_tag" ("id", "book_id", "tag_id") VALUES (4,	4,	1);

CREATE TABLE "borrowing" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "book_id" integer NOT NULL,
  "borrower_id" integer NOT NULL,
  "date" text NOT NULL,
  FOREIGN KEY ("book_id") REFERENCES "book" ("id") ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY ("borrower_id") REFERENCES "borrower" ("id") ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO "borrowing" ("id", "book_id", "borrower_id", "date") VALUES (1,	1,	1,	'2012-04-01');
INSERT INTO "borrowing" ("id", "book_id", "borrower_id", "date") VALUES (2,	3,	1,	'2013-01-02');
INSERT INTO "borrowing" ("id", "book_id", "borrower_id", "date") VALUES (3,	4,	3,	'2012-03-06');
INSERT INTO "borrowing" ("id", "book_id", "borrower_id", "date") VALUES (4,	4,	3,	'2012-05-06');
INSERT INTO "borrowing" ("id", "book_id", "borrower_id", "date") VALUES (5,	1,	3,	'2012-05-06');
