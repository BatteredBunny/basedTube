--
-- PostgreSQL database dump
--

-- Dumped from database version 14.0 (Debian 14.0-1.pgdg110+1)
-- Dumped by pg_dump version 14.0 (Debian 14.0-1.pgdg110+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: forum; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA forum;


ALTER SCHEMA forum OWNER TO postgres;

--
-- Name: stuff; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA stuff;


ALTER SCHEMA stuff OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: replies; Type: TABLE; Schema: forum; Owner: postgres
--

CREATE TABLE forum.replies (
    author uuid,
    id uuid DEFAULT gen_random_uuid(),
    date timestamp without time zone DEFAULT now(),
    content text,
    thread uuid,
    creation_ip inet
);


ALTER TABLE forum.replies OWNER TO postgres;

--
-- Name: threads; Type: TABLE; Schema: forum; Owner: postgres
--

CREATE TABLE forum.threads (
    thread_name text,
    author uuid,
    id uuid DEFAULT gen_random_uuid(),
    date timestamp without time zone DEFAULT now(),
    last_reply timestamp without time zone DEFAULT now(),
    creation_ip inet
);


ALTER TABLE forum.threads OWNER TO postgres;

--
-- Name: admins; Type: TABLE; Schema: stuff; Owner: postgres
--

CREATE TABLE stuff.admins (
    id uuid
);


ALTER TABLE stuff.admins OWNER TO postgres;

--
-- Name: comments; Type: TABLE; Schema: stuff; Owner: postgres
--

CREATE TABLE stuff.comments (
    author uuid,
    content text,
    video uuid,
    date timestamp without time zone DEFAULT transaction_timestamp(),
    id uuid DEFAULT gen_random_uuid(),
    creation_ip inet
);


ALTER TABLE stuff.comments OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: stuff; Owner: postgres
--

CREATE TABLE stuff.users (
    username text,
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    hash_password text,
    register_date timestamp without time zone DEFAULT transaction_timestamp(),
    creation_ip inet,
    avatar_id uuid DEFAULT gen_random_uuid()
);


ALTER TABLE stuff.users OWNER TO postgres;

--
-- Name: videos; Type: TABLE; Schema: stuff; Owner: postgres
--

CREATE TABLE stuff.videos (
    name text,
    views integer DEFAULT 0,
    date timestamp without time zone DEFAULT transaction_timestamp(),
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    file_name text,
    description text,
    author uuid,
    creation_ip inet,
    visibility integer DEFAULT 0,
    loop_video boolean DEFAULT false,
    file_id uuid DEFAULT gen_random_uuid(),
    thumbnail_id uuid DEFAULT gen_random_uuid()
);


ALTER TABLE stuff.videos OWNER TO postgres;

--
-- Data for Name: replies; Type: TABLE DATA; Schema: forum; Owner: postgres
--

COPY forum.replies (author, id, date, content, thread, creation_ip) FROM stdin;
\.


--
-- Data for Name: threads; Type: TABLE DATA; Schema: forum; Owner: postgres
--

COPY forum.threads (thread_name, author, id, date, last_reply, creation_ip) FROM stdin;
\.


--
-- Data for Name: admins; Type: TABLE DATA; Schema: stuff; Owner: postgres
--

COPY stuff.admins (id) FROM stdin;
\.


--
-- Data for Name: comments; Type: TABLE DATA; Schema: stuff; Owner: postgres
--

COPY stuff.comments (author, content, video, date, id, creation_ip) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: stuff; Owner: postgres
--

COPY stuff.users (username, id, hash_password, register_date, creation_ip, avatar_id) FROM stdin;
\.


--
-- Data for Name: videos; Type: TABLE DATA; Schema: stuff; Owner: postgres
--

COPY stuff.videos (name, views, date, id, file_name, description, author, creation_ip, visibility, loop_video, file_id, thumbnail_id) FROM stdin;
\.


--
-- Name: users users_un; Type: CONSTRAINT; Schema: stuff; Owner: postgres
--

ALTER TABLE ONLY stuff.users
    ADD CONSTRAINT users_un UNIQUE (username);


--
-- PostgreSQL database dump complete
--

