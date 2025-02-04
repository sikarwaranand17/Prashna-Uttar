CREATE TABLE `p_ads` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `content` text NOT NULL,
  `link` text NOT NULL,
  `created_at` datetime NOT NULL,
  `expiry` varchar(99) NOT NULL,
  `views` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  `location` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `p_answers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `q_id` int(11) NOT NULL,
  `a_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `likes` int(11) NOT NULL,
  `dislikes` int(11) NOT NULL,
  `anonymous` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `published` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `p_awards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `p_chat` (
  `id` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `msg` varchar(300) NOT NULL,
  `sent_at` datetime NOT NULL,
  `viewed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `p_file_upload` (
  `id` int(11) NOT NULL,
  `filename` varchar(30) NOT NULL,
  `type` varchar(30) NOT NULL,
  `size` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `p_follows_rules` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `obj_id` int(11) NOT NULL,
  `follow_date` datetime NOT NULL,
  `obj_type` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `p_functions` (
  `id` int(11) NOT NULL,
  `function` varchar(30) NOT NULL,
  `value` text NOT NULL,
  `msg` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='misc functions goes here';


INSERT INTO `p_functions` (`id`, `function`, `value`, `msg`) VALUES
(1, 'general_settings', 'a:14:{s:9:"site_name";s:7:"Pearls!";s:9:"site_logo";i:0;s:16:"site_description";s:21:"Questions and Answers";s:13:"site_keywords";s:23:"Questions,Answers,Quora";s:11:"site_status";s:1:"1";s:9:"site_lang";s:7:"English";s:11:"closure_msg";s:48:"Site closed for maintenance.. please stay tuned!";s:8:"url_type";s:4:"slug";s:10:"q_approval";s:1:"0";s:10:"a_approval";s:1:"0";s:22:"spaces_classifications";s:57:"General,Music,Movies,History,World,Photography,Technology";s:9:"reg_group";s:1:"3";s:6:"social";s:1:"1";s:13:"public_access";s:1:"1";}', ''),
(2, 'privacy-policy', '', ''),
(3, 'about-us', '', ''),
(4, 'contact-us', '', 'michael.zohney@gmail.com'),
(5, 'terms', '', ''),
(6, 'profanity_filter', 'a55,a55hole,aeolus,ahole,anal,analprobe,anilingus,anus,areola,areole,arian,aryan,ass,assbang,assbanged,assbangs,asses,assfuck,assfucker,assh0le,asshat,assho1e,ass hole,assholes,assmaster,assmunch,asswipe,asswipes,azazel,azz,b1tch,babe,babes,ballsack,bang,banger,barf,bastard,bastards,bawdy,beaner,beardedclam,beastiality,beatch,beater,beaver,beer,beeyotch,beotch,biatch,bigtits,big tits,bimbo,bitch,bitched,bitches,bitchy,blow job,blow,blowjob,blowjobs,bod,bodily,boink,bollock,bollocks,bollok,bone,boned,boner,boners,bong,boob,boobies,boobs,booby,booger,bookie,bootee,bootie,booty,booze,boozer,boozy,bosom,bosomy,bowel,bowels,bra,brassiere,breast,breasts,bugger,bukkake,bullshit,bull shit,bullshits,bullshitted,bullturds,bung,busty,butt,butt fuck,buttfuck,buttfucker,buttfucker,buttplug,c.0.c.k,c.o.c.k.,c.u.n.t,c0ck,c-0-c-k,caca,cahone,cameltoe,carpetmuncher,cawk,cervix,chinc,chincs,chink,chink,chode,chodes,cl1t,climax,clit,clitoris,clitorus,clits,clitty,cocain,cocaine,cock,c-o-c-k,cockblock,cockholster,cockknocker,cocks,cocksmoker,cocksucker,cock sucker,coital,commie,condom,coon,coons,corksucker,crabs,crack,cracker,crackwhore,crap,crappy,cum,cummin,cumming,cumshot,cumshots,cumslut,cumstain,cunilingus,cunnilingus,cunny,cunt,cunt,c-u-n-t,cuntface,cunthunter,cuntlick,cuntlicker,cunts,d0ng,d0uch3,d0uche,d1ck,d1ld0,d1ldo,dago,dagos,dammit,damn,damned,damnit,dawgie-style,dick,dickbag,dickdipper,dickface,dickflipper,dickhead,dickheads,dickish,dick-ish,dickripper,dicksipper,dickweed,dickwhipper,dickzipper,diddle,dike,dildo,dildos,diligaf,dillweed,dimwit,dingle,dipship,doggie-style,doggy-style,dong,doofus,doosh,dopey,douch3,douche,douchebag,douchebags,douchey,drunk,dumass,dumbass,dumbasses,dummy,dyke,dykes,ejaculate,enlargement,erect,erection,erotic,essohbee,extacy,extasy,f.u.c.k,fack,fag,fagg,fagged,faggit,faggot,fagot,fags,faig,faigt,fannybandit,fart,fartknocker,fat,felch,felcher,felching,fellate,fellatio,feltch,feltcher,fisted,fisting,fisty,floozy,foad,fondle,foobar,foreskin,freex,frigg,frigga,fubar,fuck,f-u-c-k,fuckass,fucked,fucked,fucker,fuckface,fuckin,fucking,fucknugget,fucknut,fuckoff,fucks,fucktard,fuck-tard,fuckup,fuckwad,fuckwit,fudgepacker,fuk,fvck,fxck,gae,gai,ganja,gay,gays,gey,gfy,ghay,ghey,gigolo,glans,goatse,godamn,godamnit,goddam,goddammit,goddamn,goldenshower,gonad,gonads,gook,gooks,gringo,gspot,g-spot,gtfo,guido,h0m0,h0mo,handjob,hard on,he11,hebe,heeb,hell,hemp,heroin,herp,herpes,herpy,hitler,hiv,hobag,hom0,homey,homo,homoey,honky,hooch,hookah,hooker,hoor,hootch,hooter,hooters,horny,hump,humped,humping,hussy,hymen,inbred,incest,injun,j3rk0ff,jackass,jackhole,jackoff,jap,japs,jerk,jerk0ff,jerked,jerkoff,jism,jiz,jizm,jizz,jizzed,junkie,junky,kike,kikes,kill,kinky,kkk,klan,knobend,kooch,kooches,kootch,kraut,kyke,labia,lech,leper,lesbians,lesbo,lesbos,lez,lezbian,lezbians,lezbo,lezbos,lezzie,lezzies,lezzy,lmao,lmfao,loin,loins,lube,lusty,mams,massa,masterbate,masterbating,masterbation,masturbate,masturbating,masturbation,maxi,menses,menstruate,menstruation,meth,m-fucking,mofo,molest,moolie,moron,motherfucka,motherfucker,motherfucking,mtherfucker,mthrfucker,mthrfucking,muff,muffdiver,murder,muthafuckaz,muthafucker,mutherfucker,mutherfucking,muthrfucking,nad,nads,naked,napalm,nappy,nazi,nazism,negro,nigga,niggah,niggas,niggaz,nigger,nigger,niggers,niggle,niglet,nimrod,ninny,nipple,nooky,nympho,opiate,opium,oral,orally,organ,orgasm,orgasmic,orgies,orgy,ovary,ovum,ovums,p.u.s.s.y.,paddy,paki,pantie,panties,panty,pastie,pasty,pcp,pecker,pedo,pedophile,pedophilia,pedophiliac,pee,peepee,penetrate,penetration,penial,penile,penis,perversion,peyote,phalli,phallic,phuck,pillowbiter,pimp,pinko,piss,pissed,pissoff,piss-off,pms,polack,pollock,poon,poontang,porn,porno,pornography,pot,potty,prick,prig,prostitute,prude,pube,pubic,pubis,punkass,punky,puss,pussies,pussy,pussypounder,puto,queaf,queef,queef,queer,queero,queers,quicky,quim,racy,rape,raped,raper,rapist,raunch,rectal,rectum,rectus,reefer,reetard,reich,retard,retarded,revue,rimjob,ritard,rtard,r-tard,rum,rump,rumprammer,ruski,s.h.i.t.,s.o.b.,s0b,sadism,sadist,scag,scantily,schizo,schlong,screw,screwed,scrog,scrot,scrote,scrotum,scrud,scum,seaman,seamen,seduce,semen,sex,sexual,sh1t,s-h-1-t,shamedame,shit,s-h-i-t,shite,shiteater,shitface,shithead,shithole,shithouse,shits,shitt,shitted,shitter,shitty,shiz,sissy,skag,skank,slave,sleaze,sleazy,slut,slutdumper,slutkiss,sluts,smegma,smut,smutty,snatch,sniper,snuff,s-o-b,sodom,souse,soused,sperm,spic,spick,spik,spiks,spooge,spunk,steamy,stfu,stiffy,stoned,strip,stroke,stupid,suck,sucked,sucking,sumofabiatch,t1t,tampon,tard,tawdry,teabagging,teat,terd,teste,testee,testes,testicle,testis,thrust,thug,tinkle,tit,titfuck,titi,tits,tittiefucker,titties,titty,tittyfuck,tittyfucker,toke,toots,tramp,transsexual,trashy,tubgirl,turd,tush,twat,twats,ugly,undies,unwed,urinal,urine,uterus,uzi,vag,vagina,valium,viagra,virgin,vixen,vodka,vomit,voyeur,vulgar,vulva,wad,wang,wank,wanker,wazoo,wedgie,weed,weenie,weewee,weiner,weirdo,wench,wetback,wh0re,wh0reface,whitey,whiz,whoralicious,whore,whorealicious,whored,whoreface,whorehopper,whorehouse,whores,whoring,wigger,womb,woody,wop,wtf,x-rated,xxx,yeasty,yobbo,zoophile,cock', ''),
(7, 'admanager1', '', ''),
(8, 'admanager2', '', '');


CREATE TABLE `p_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `privileges` text NOT NULL,
  `deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `p_groups` (`id`, `name`, `privileges`, `deleted`) VALUES
(1, 'Admin', 'index.read,pages.read,error-404.read,index.notifications,index.post,index.feed,feed.follow,questions.read,questions.interact,post.read,questions.create,questions.power,questions.update,questions.delete,spaces.read,spaces.interact,spaces.read,spaces.create,spaces.power,spaces.update,spaces.delete,answers.read,answers.create,answers.power,answers.update,answers.delete,users.read,users.follow,users.update,users.changepass,users.changemail,users.delete,admin.read,dashboard.read,general_settings.update,profanity_filter.update,pending.read,pending.update,pages.read,pages.update,adminusers.read,adminusers.update,adminusers.changepass,adminusers.changemail,adminusers.changeusername,adminusers.power,adminusers.suspend,adminusers.delete,admintopics.read,admintopics.update,admintopics.delete,admanager.read,admanager.create,admanager.update,admanager.delete,groups.read,groups.create,groups.update,groups.delete', 0),
(2, 'Guest', 'index.read,pages.read,error-404.read,questions.read,spaces.read,users.read,pages.read', 0),
(3, 'Registered Users', 'index.read,pages.read,error-404.read,index.notifications,index.post,index.feed,feed.follow,questions.read,questions.interact,post.read,questions.create,questions.update,spaces.read,spaces.interact,spaces.read,spaces.create,spaces.update,spaces.delete,answers.read,answers.create,answers.update,users.read,users.follow,users.update,users.changepass,users.delete,pages.read', 0);

CREATE TABLE `p_likes_rules` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `obj_id` int(11) NOT NULL,
  `like_date` datetime NOT NULL,
  `obj_type` varchar(30) NOT NULL,
  `type` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `p_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `action` varchar(30) NOT NULL,
  `msg` text NOT NULL,
  `done_at` datetime NOT NULL,
  `ip` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `p_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `msg` MEDIUMTEXT NOT NULL,
  `link` varchar(300) NOT NULL,
  `created_at` datetime NOT NULL,
  `viewed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `p_questions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `space_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `feed` varchar(99) NOT NULL,
  `content` text NOT NULL,
  `likes` int(11) NOT NULL,
  `dislikes` int(11) NOT NULL,
  `answers` int(11) NOT NULL,
  `follows` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `shares` int(11) NOT NULL,
  `slug` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `published` tinyint(1) NOT NULL,
  `anonymous` tinyint(1) NOT NULL,
  `item_type` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `p_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `obj_id` int(11) NOT NULL,
  `obj_type` varchar(30) NOT NULL,
  `report_date` datetime NOT NULL,
  `info` varchar(300) NOT NULL,
  `result` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `p_spaces` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admins` varchar(500) NOT NULL,
  `moderators` varchar(500) NOT NULL,
  `contributors` varchar(500) NOT NULL,
  `name` varchar(99) NOT NULL,
  `tagline` varchar(300) NOT NULL,
  `description` varchar(500) NOT NULL,
  `feed` varchar(300) NOT NULL,
  `follows` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `slug` varchar(99) NOT NULL,
  `created_at` datetime NOT NULL,
  `avatar` int(11) NOT NULL,
  `cover` int(11) NOT NULL,
  `open_post` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `p_tags` (
  `id` int(11) NOT NULL,
  `name` varchar(99) NOT NULL,
  `follows` int(11) NOT NULL,
  `description` text NOT NULL,
  `avatar` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `used` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `p_tags` (`id`, `name`, `follows`, `description`, `avatar`, `deleted`, `used`) VALUES
(1, 'General', 0, '', 0, 0, 0);

CREATE TABLE `p_users` (
  `id` int(11) NOT NULL,
  `password` varchar(99) NOT NULL,
  `prvlg_group` int(11) NOT NULL,
  `email` varchar(99) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `avatar` int(11) NOT NULL,
  `username` varchar(99) NOT NULL,
  `f_name` varchar(30) NOT NULL,
  `l_name` varchar(30) NOT NULL,
  `comment` varchar(199) NOT NULL,
  `about` varchar(300) NOT NULL,
  `hybridauth_provider_name` varchar(255) NOT NULL,
  `hybridauth_provider_uid` varchar(255) NOT NULL,
  `follows` int(11) NOT NULL,
  `ban_list` text NOT NULL,
  `points` int(11) NOT NULL,
  `joined` datetime NOT NULL,
  `last_seen` varchar(11) NOT NULL,
  `intro` tinyint(1) NOT NULL,
  `disabled` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `mail_notif` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='users table to access cp';

INSERT INTO `p_users` (`id`, `password`, `prvlg_group`, `email`, `mobile`, `address`, `avatar`, `username`, `f_name`, `l_name`, `comment`, `about`, `hybridauth_provider_name`, `hybridauth_provider_uid`, `follows`, `ban_list`, `points`, `joined`, `last_seen`, `intro`, `disabled`, `deleted`, `mail_notif`) VALUES
(1, '$P$BBq/cHsRnflX4j9KLZUzwSZ61YfaKi0', 1, 'michael.zohney@gmail.com', '', '', 0, 'admin', 'Michael', 'Johny', '', '', '', '', 0, '', 0, '0000-00-00 00:00:00', '0', 0, 0, 0, 'new-user-follow,new-question-follow,approve-question,approve-answer,reject-question,reject-answer,report-my-questions,report-my-answers,report-others-questions,report-others-answers,question-report-rejected,answer-report-rejected,new-user-question,new-feed-question,mention,new-answer'),
(1000, '$P$BmTGHpIQ2EiRn9oa121LQgnvsMlTex/', 2, 'guest', '', '', 0, '', 'Guest', '', '', '', '', '', 0, '', 0, '0000-00-00 00:00:00', '', 1, 0, 0, '');


ALTER TABLE `p_ads`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_answers`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_awards`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_chat`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_file_upload`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_follows_rules`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_functions`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_groups`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_likes_rules`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `p_logs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `p_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);


ALTER TABLE `p_questions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `p_reports`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_spaces`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_tags`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `p_users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `p_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `p_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `p_awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `p_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `p_file_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_follows_rules`
--
ALTER TABLE `p_follows_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_functions`
--
ALTER TABLE `p_functions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `p_groups`
--
ALTER TABLE `p_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `p_likes_rules`
--
ALTER TABLE `p_likes_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_logs`
--
ALTER TABLE `p_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_notifications`
--
ALTER TABLE `p_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_questions`
--
ALTER TABLE `p_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_reports`
--
ALTER TABLE `p_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_spaces`
--
ALTER TABLE `p_spaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_tags`
--
ALTER TABLE `p_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `p_users`
--
ALTER TABLE `p_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
