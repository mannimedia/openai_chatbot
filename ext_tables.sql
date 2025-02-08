CREATE TABLE tx_openai_chatbot_domain_model_chatthread (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    thread_id varchar(255) DEFAULT '' NOT NULL,
    assistant_id varchar(255) DEFAULT '' NOT NULL,
    messages mediumtext,
    last_activity int(11) DEFAULT '0' NOT NULL,

    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY thread (thread_id)
);

CREATE TABLE pages (
    tx_openai_chatbot_disabled tinyint(4) unsigned DEFAULT '0' NOT NULL
);

