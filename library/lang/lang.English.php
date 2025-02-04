<?php 

$lang = Array();
$lang['lang'] = 'en';
$lang['direction'] = 'ltr';
$lang['direction-right'] = 'right';
$lang['direction-left'] = 'left';

//Alerts
$lang['alert-type-success'] = 'Success';
$lang['alert-type-error'] = 'Error';
$lang['alert-restricted'] = 'Restricted access! You are not authorized to view this section';
$lang['alert-email_exists'] = 'Email already exists in database! please try again';
$lang['alert-password_reset'] = "Password resetted successfully! Please check your Email for new login credentials.";
$lang['alert-user_not_found'] = "User not found on system, please try again";
$lang['alert-auth_error'] = "Authentication Error! Please try again";
$lang['alert-captcha_error'] = "Captcha Error! Please try again";
$lang['alert-accept_terms'] = "You must accept Terms and Conditions before registering new account with us";
$lang['alert-account_created'] = "Account created successfully! welcome to";
$lang['alert-account_failed'] = "Account creation failed! Please try registering again";
$lang['alert-invalid_pass'] = "Invalid Password, please try again";
$lang['alert-invalid_user'] = "User not found in database, please try again";
$lang['alert-upload_error'] = "Unable to upload files, errors found";
$lang['alert-create_success'] = "Data created successfully";
$lang['alert-create_failed'] = "Unable to save data, please try again";
$lang['alert-update_success'] = "Data updated successfully";
$lang['alert-update_failed'] = "Unable to update data, please try again";
$lang['alert-delete_success'] = "Data deleted successfully";
$lang['alert-delete_failed'] = "Unable to delete data, please try again";

//Buttons
$lang['btn-register'] = 'Register';
$lang['btn-add'] = 'Add';
$lang['btn-edit'] = 'Edit';
$lang['btn-delete'] = 'Delete';
$lang['btn-login'] = 'Login';
$lang['btn-submit'] = 'Submit';
$lang['btn-update'] = 'Update';
$lang['btn-cancel'] = 'Cancel';
$lang['btn-back'] = 'Back';
$lang['btn-close'] = 'Close';
$lang['btn-reset_pass'] = 'Reset Password';
$lang['btn-follow'] = 'Follow';
$lang['btn-followed'] = 'Following';
$lang['btn-reply'] = 'Reply';
$lang['btn-like'] = 'Like';
$lang['btn-liked'] = 'Liked';
$lang['btn-likes'] = 'Likes';
$lang['btn-dislike'] = 'Dislike';
$lang['btn-disliked'] = 'Disliked';
$lang['btn-dislikes'] = 'Dislikes';
$lang['btn-answers'] = 'Answers';
$lang['btn-views'] = 'Views';
$lang['btn-tools'] = 'Tools';
$lang['btn-close_account'] = 'Close Account';

//Index page
$lang['index-search-title'] = "What's in your mind";
$lang['index-search-button'] = 'Add Question';
$lang['index-search-questions'] = 'Questions about';
$lang['index-read-button'] = 'Read';
$lang['index-notification-button'] = 'Notifications';
$lang['index-notification-see_all'] = 'See all Notifications';
$lang['index-notification-no_results'] = 'No New Notifications';
$lang['index-notification-no_notifications'] = 'No New Notifications';
$lang['index-admin-button'] = 'Admin';
$lang['index-user-admin'] = 'Admin Panel';
$lang['index-user-profile'] = 'Profile';
$lang['index-user-settings'] = 'Settings';
$lang['index-user-logout'] = 'Logout';
$lang['index-user-login'] = 'Login';
$lang['index-leaderboard-points'] = 'Points';
$lang['index-question-submit'] = 'Submit your Question';
$lang['index-question-intro'] = 'This question has [VIEWS] views, [ANSWERS] answers';
$lang['index-question-no_questions'] = 'No questions found';
$lang['index-question-post'] = 'Add new question';
$lang['index-question-created'] = 'Posted';
$lang['index-question-updated'] = 'Updated';
$lang['index-question-read_more'] = 'More';
$lang['index-question-answer'] = 'Answer';
$lang['index-sidebar-welcome'] = 'Welcome';
$lang['index-sidebar-feeds'] = 'Topics';
$lang['index-sidebar-top'] = 'Newest Stories';
$lang['index-sidebar-trending'] = 'Trending';
$lang['index-sidebar-subscriptions'] = 'Subscriptions';
$lang['index-sidebar-related_questions'] = 'Related Questions';
$lang['index-sidebar-your_questions'] = 'Your Questions';
$lang['index-sidebar-your_answers'] = 'Your Answers';

//Admin page
$lang['admin-title'] = 'Site Admin';
$lang['admin-hello'] = 'Hello';
$lang['admin-section-dashboard'] = 'Dashboard';
$lang['admin-section-general'] = 'General Settings';
$lang['admin-section-filter'] = 'Bad Words Filter';
$lang['admin-section-pending'] = 'Pending Approval';
$lang['admin-section-users'] = 'Users';
$lang['admin-section-groups'] = 'Groups';
$lang['admin-section-pages'] = 'Edit Pages';
$lang['admin-dashboard-users'] = "You've <b>[COUNT]</b> registered users on your site, view them <a href='{$url_mapper['admin/users']}' >Here</a><br>Here's your user registration frequency for the last 3 weeks";
$lang['admin-dashboard-questions'] = "You've <b>[COUNT]</b> questions posted on your site<br>Here's your question posting frequency for the last 3 weeks";
$lang['admin-dashboard-answers'] = "You've <b>[COUNT]</b> answers posted on your site<br>Here's your answers posting frequency for the last 3 weeks";
$lang['admin-pages-title'] = "Edit Pages";
$lang['admin-pages-about-email'] = "Contact Email";
$lang['admin-filter-title'] = "Bad words filter Settings";
$lang['admin-general-title'] = "General Settings";
$lang['admin-general-site-title'] = "- Site Settings -";
$lang['admin-general-site-lang'] = "Site Language";
$lang['admin-general-site-name'] = "Site Name";
$lang['admin-general-site-logo'] = "Site Logo";
$lang['admin-general-site-description'] = "Site Description";
$lang['admin-general-site-keywords'] = "Keywords";
$lang['admin-general-site-status'] = "Site Status";
$lang['admin-general-site-status_msg'] = "Closure Message";
$lang['admin-general-url-title'] = "- URL Settings -";
$lang['admin-general-url-type'] = "URL Type";
$lang['admin-general-posting-title'] = "- Posting Settings -";
$lang['admin-general-posting-questions'] = "Publishing a question";
$lang['admin-general-posting-answers'] = "Publishing an answer";
$lang['admin-general-access-title'] = "- Public Access Settings -";
$lang['admin-general-access-login'] = "Public access without login";
$lang['admin-general-reg-title'] = "- Registration Settings -";
$lang['admin-general-reg-group'] = "Default registration group";
$lang['admin-pending-title'] = "Posts awaiting admin approval";
$lang['admin-pending-questions'] = "- Questions -";
$lang['admin-pending-questions-title'] = "Title";
$lang['admin-pending-questions-user'] = "Posted by";
$lang['admin-pending-answers'] = "- Answers -";
$lang['admin-pending-answers-comment'] = "Comment";
$lang['admin-pending-answers-user'] = "Posted by";
$lang['admin-users-title'] = "Registered Users";
$lang['admin-users-f_name'] = "First Name";
$lang['admin-users-l_name'] = "Last Name";
$lang['admin-users-phone'] = "Phone";
$lang['admin-users-address'] = "Address";
$lang['admin-users-group'] = "Privilege";
$lang['admin-users-comment'] = "Short description";
$lang['admin-users-about'] = "About";
$lang['admin-users-avatar'] = "Avatar";
$lang['admin-users-email'] = "Email";
$lang['admin-users-pass'] = "Password";
$lang['admin-users-suspend'] = "Suspend Account";
$lang['admin-users-questions'] = "Questions";
$lang['admin-users-answers'] = "Answers";
$lang['admin-groups-title'] = "Privilege Groups";
$lang['admin-groups-name'] = "Group Name";
$lang['admin-groups-users'] = "Users";

//Login page
$lang['login-logged_out'] = 'Logged out Successfully!';
$lang['login-using_facebook'] = 'Login Using Facebook';
$lang['login-using_google'] = 'Login Using Google';
$lang['login-register'] = 'You can <a href="#me" id="register">Register New Account</a>';
$lang['login-remember'] = 'Remember Me';
$lang['login-forgot_pass'] = 'Forgot your password ?';
$lang['login-as_guest'] = "or <a href='{$url_mapper['index/']}' class=''>Login as Guest</a>";
$lang['login-privacy'] = 'By signing up you indicate that you have read and agree to the <a href="#privacy_policy" data-toggle="modal">Privacy Policy</a>.';
$lang['login-register-f_name'] = 'First Name';
$lang['login-register-l_name'] = 'Last Name';
$lang['login-register-email'] = 'Email';
$lang['login-register-pass'] = 'Password';
$lang['login-register-terms'] = 'by registering, I accept <a href="#terms" data-toggle="modal">Terms and Conditions</a>';

//Questions
$lang['questions-pending'] = " pending admin confirmation to become public and start receiving pretty useful answers ;)";
$lang['questions-pending-tag'] = " Awaiting Admin Approval";
$lang['questions-title'] = "Submit Your Question";
$lang['questions-q_title'] = "Question title";
$lang['questions-anonymous'] = "Anonymous";
$lang['questions-tags'] = "Keywords";
$lang['questions-details'] = "Question Details";
$lang['questions-answer-create_success'] = "Your answer was submitted! Thanks for contributing in such interesting discussion";
$lang['questions-answer-create_failed'] = "Cannot submit your answer! please try again";
$lang['questions-answer-update_success'] = "Your answer was updated! Thanks for contributing in such interesting discussion";
$lang['questions-answer-update_failed'] = "Cannot submit your answer! please try again";
$lang['questions-approve'] = "Approve question";
$lang['questions-edit'] = "Edit question";
$lang['questions-delete'] = "Delete question";
$lang['questions-report'] = "Report question";
$lang['questions-answer-report'] = "Report answer";
$lang['questions-answer-create'] = "Submit Answer";
$lang['questions-answer-update'] = "Update Answer";

//Site Pages
$lang['pages-about-title'] = 'About Us';
$lang['pages-contact-title'] = 'Contact Us';
$lang['pages-contact-success'] = "Message sent successfully, Thanks for your feedback";
$lang['pages-contact-fail'] = "Message delivery failed, please try again";
$lang['pages-contact-name'] = "Name";
$lang['pages-contact-email'] = "Email";
$lang['pages-contact-msg_title'] = "Message Title";
$lang['pages-contact-msg_details'] = "Message Details";
$lang['pages-contact-captcha'] = "Captcha";
$lang['pages-contact-send'] = "Send Message";
$lang['pages-privacy-title'] = 'Privacy Policy';
$lang['pages-terms-title'] = 'Terms & Conditions';
$lang['pages-leaderboard-title'] = 'Leaderboard';
$lang['pages-notifications-title'] = 'Your Notifications';
$lang['pages-notifications-read_all'] = 'Mark all as read';

//users
$lang['user-anonymous'] = 'Anonymous';
$lang['user-anonymous-intro'] = 'Post Anonymously, Visible in this list only to you!';
$lang['user-account-options'] = 'Account Options';
$lang['user-account-edit'] = 'Edit Account';
$lang['user-account-delete'] = 'Delete Account';
$lang['user-farewell'] = 'Farewell [NAME]! if you changed your mind anytime and want to recover your account again, please contact us at [EMAIL]';
$lang['user-sections'] = 'Sections';
$lang['user-questions'] = 'Questions';
$lang['user-answers'] = 'Answers';
$lang['user-followed'] = 'Followed by';
$lang['user-following'] = 'Following';
$lang['user-points'] = 'Points';
$lang['user-comment-read_more'] = 'Read Full Comment';
$lang['user-comment-posted_at'] = 'Posted at';
$lang['user-delete-msg'] = "Are you sure you want to close your account? this couldn't be undone";
$lang['user-points-reason'] = "Reason";
$lang['user-points-awarded_at'] = "Awarded at";

//Notifications & Emails
$lang['notif-q_publish-title'] = "Question Approved";
$lang['notif-q_publish-msg'] = "Your question ([TITLE]) was published successfully";
$lang['notif-q_reject-title'] = "Question Rejected";
$lang['notif-q_reject-msg'] = "Your question ([TITLE]) was rejected by admins! and deleted from our system";
$lang['notif-a_publish-title'] = "Answer Approved";
$lang['notif-a_publish-msg'] = "Your answer for question ([TITLE]) was published successfully";
$lang['notif-a_reject-title'] = "Answer Rejected";
$lang['notif-a_reject-msg'] = "Your answer for question ([TITLE]) was rejected by admins! and deleted from our system";
$lang['notif-a_publish-follow-title'] = "New Answer Posted";
$lang['notif-a_publish-follow-msg'] = "([NAME]) Posted a new answer to question ([TITLE])";
$lang['notif-user-follow-title'] = "Someone followed you";
$lang['notif-user-follow-msg'] = "<a href='[LINK]/'>[NAME]</a> is following you.";
$lang['notif-award'] = "You've been awarded";
$lang['notif-point'] = "point";
$lang['notif-q_f_award-title'] = "Someone followed one of your questions";
$lang['notif-q_f_award-msg'] = "<a href='[LINK]/'>[NAME]</a> is following <a href='[Q_LINK]'>one of your questions</a>";
$lang['notif-q_l_award'] = "<a href='[LINK]/'>[NAME]</a> liked <a href='[Q_LINK]'>one of your questions</a>";
$lang['notif-a_l_award'] = "<a href='[LINK]/'>[NAME]</a> liked <a href='[Q_LINK]'>one of your answers</a>";

//Updates
$lang['questions-report-info'] = 'Reason for reporting this content';
$lang['questions-report-reported'] = "You've already reported this content before";
$lang['admin-section-reports'] = "Reports";
$lang['admin-reports-title'] = "Pending Reports";
$lang['admin-reports-post'] = "Content";
$lang['admin-reports-user'] = "User";
$lang['admin-reports-info'] = "Reason";
$lang['admin-reports-type-q'] = "Question";
$lang['admin-reports-type-a'] = "Answer";
$lang['admin-reports-approve_report'] = "Remove";
$lang['admin-reports-approve_report-alert'] = "Are you sure you want to remove this content from website?";
$lang['admin-reports-reject_report'] = "Ignore";
$lang['admin-reports-reject_report-alert'] = "Are you sure you want to reject this report?";
$lang['notif-report-q_publisher-approve-title'] = "Content removed based on users reports";
$lang['notif-report-q_publisher-approve-msg'] = "Your question ([TITLE]) removed from website based on users reports, please stick to our <a href='{$url_mapper['pages/view']}terms'>Terms and Conditions</a> in your future posts";
$lang['notif-report-q_reporter-approve-title'] = "Content removed based on your report";
$lang['notif-report-q_reporter-approve-msg'] = "Question ([TITLE]) removed from website based on your report, thanks for reporting";
$lang['notif-report-a_publisher-approve-title'] = "Content removed based on users reports";
$lang['notif-report-a_publisher-approve-msg'] = "Your answer to the question ([TITLE]) removed from website based on users reports, please stick to our <a href='{$url_mapper['pages/view']}terms'>Terms and Conditions</a> in your future posts";
$lang['notif-report-a_reporter-approve-title'] = "Content removed based on your report";
$lang['notif-report-a_reporter-approve-msg'] = "Answer to the question ([TITLE]) removed from website based on your report, thanks for reporting";
$lang['notif-report-q_reporter-reject-title'] = "Report reviewd, Content not removed";
$lang['notif-report-q_reporter-reject-msg'] = "Your report about the question ([TITLE]) was reviewd and it doesn't violate our <a href='{$url_mapper['pages/view']}terms'>Terms and Conditions</a>, so it won't be removed from website";
$lang['notif-report-a_reporter-reject-title'] = "Report reviewd, Content not removed";
$lang['notif-report-a_reporter-reject-msg'] = "Your report about answer to the question ([TITLE]) was reviewd and it doesn't violate our <a href='{$url_mapper['pages/view']}terms'>Terms and Conditions</a>, so it won't be removed from website";
$lang['alert-report_success'] = "Content reported successfully and will be reviewd by admins";
$lang['alert-report_failed'] = "Content cannot be reported, please try again later";
$lang['notif-question-create-title'] = "New Post";
$lang['notif-question-create-msg'] = "(%s) Posted a new Question (%s)";
$lang['notif-question-tag-create-title'] = "New Post";
$lang['notif-question-tag-create-msg'] = "New Question posted Regarding (%s)";

$lang['btn-go_to_q'] = "Full Conversation";
$lang['admin-section-topics'] = "Edit Topics";
$lang['admin-topics-title'] = "Edit Site Topics";
$lang['admin-topics-name'] = "Topic Name";
$lang['admin-topics-description'] = "Topic Description";
$lang['admin-topics-avatar'] = "Avatar";

$lang['admin-section-admanager'] = "Ads Manager";
$lang['admin-admanager-title'] = "Ads Manager";
$lang['admin-admanager-lt_sidebar'] = "Left Sidebar";
$lang['admin-admanager-rt_sidebar'] = "Right Sidebar";
$lang['admin-admanager-between_q'] = "Between Questions";
$lang['admin-admanager-between_a'] = "Between Answers";

$lang['notif-a_mention-title'] = "New mention for you";
$lang['notif-a_mention-msg'] = "([NAME]) Has mentioned you in ([TITLE]) question, Click here and join the discussion!";
$lang['admin-users-username'] = "Username";
$lang['alert-username_exists'] = 'Username already exists in database! please try again';

$lang['welcome'] = 'Welcome';
$lang['welcome-msg'] = "Please choose your favorite topics you want to follow";

$lang['index-chat-title'] = "Chat";
$lang['index-chat-no_chat'] = "Click on one of online users to start chatting";
$lang['index-chat-no_friends'] = "No users available for chat!";
$lang['index-chat-send'] = "Send";
$lang['admin-general-chat-title'] = "- Chat Settings -";
$lang['admin-general-chat-msg'] = "Enable chatting";



$lang['questions-report-types'] = Array("Harassment: Not respectful towards a person or group",
														"Spam: Undisclosed promotion for a link or product",
														"Irrelevant: Does not address question that was asked",
														"Plagiarism: Reusing content without attribution (link and blockquotes)",
														"Joke Answer: Not a sincere answer",
														"Poorly Written: Bad formatting, grammar, and spelling",
														"Incorrect: Substantially incorrect and/or incorrect primary conclusions");
$lang['admin-general-posting-q_modal'] = "Open questions in modals";

$lang['user-notification-settings-title'] = "Send Email notifications when:";
$lang['user-notification-settings-new-user-follow'] = "Someone follows you";
$lang['user-notification-settings-new-question-follow'] = "Someone follows one of your questions";
$lang['user-notification-settings-approve-question'] = "Admin approves your question";
$lang['user-notification-settings-approve-answer'] = "Admin approves your answer";
$lang['user-notification-settings-reject-question'] = "Admin rejects your question";
$lang['user-notification-settings-reject-answer'] = "Admin rejects your answer";
$lang['user-notification-settings-report-my-questions'] = "Questions deleted upon other users reports";
$lang['user-notification-settings-report-my-answers'] = "Answers deleted upon other users reports";
$lang['user-notification-settings-report-others-questions'] = "Delete questions based on your reports";
$lang['user-notification-settings-report-others-answers'] = "Delete answers based on your reports";
$lang['user-notification-settings-question-report-rejected'] = "Reject your reports on others questions";
$lang['user-notification-settings-answer-report-rejected'] = "Reject your reports on others answers";
$lang['user-notification-settings-new-user-question'] = "Someone you follow posts new question";
$lang['user-notification-settings-new-feed-question'] = "Someone posts new question on a topic you follow";
$lang['user-notification-settings-mention'] = "Someone mentions you in a post";
$lang['user-notification-settings-new-answer'] = "Someone posts a new answer on a question you follow";

//Update 3
$lang['btn-shares'] = 'Shares';
$lang['index-search-add_p'] = 'Add Post';
$lang['index-spaces-button'] = 'Spaces';
$lang['index-feed-button'] = 'Feed';
$lang['index-add_q-classifications'] = 'Classifications';
$lang['index-question-no_answers'] = 'No answers yet!';
$lang['btn-rss'] = 'RSS Feed';
$lang['btn-view'] = 'View';
$lang['btn-search'] = 'Search';
$lang['question-delete-alert'] = "Are you sure you want to delete this content?";
$lang['answer-delete-alert'] = "Are you sure you want to delete this answer?";
$lang['questions-public'] = "Public";
$lang['questions-private'] = "Private";
$lang['questions-update'] = "Update Your Question";
$lang['index-no_posts'] = 'No answers found';
$lang['settings-no_reports'] = 'No reports found';
$lang['settings-reports-approved'] = 'Reported content removed successfully';
$lang['settings-reports-rejected'] = 'Report ignored, no data were removed';
$lang['admin-users-name'] = "Name";
$lang['index-spaces-add'] = "Add Space";
$lang['index-spaces-your_spaces'] = "Your Spaces:";
$lang['index-spaces-no_subscriptions'] = "No subscriptions found";
$lang['index-spaces-no_spaces'] = 'No Spaces found';
$lang['btn-view_more'] = 'View more';
$lang['spaces-details'] = 'Details';
$lang['spaces-people'] = 'People';
$lang['spaces-edit'] = 'Edit Space';
$lang['spaces-delete'] = 'Delete Space';
$lang['spaces-edit_post'] = 'Edit post';
$lang['spaces-delete_post'] = 'Delete post';
$lang['spaces-report_post'] = 'Report post';
$lang['spaces-report'] = 'Report Space';
$lang['spaces-add_q'] = 'Question';
$lang['spaces-add_p'] = 'Post';
$lang['spaces-pending'] = "Pending Posts";
$lang['spaces-approve'] = "Approve posts";
$lang['spaces-no_posts'] = "No posts found";
$lang['spaces-no_users'] = "No users found";
$lang['spaces-admins'] = "Admins";
$lang['spaces-moderators'] = "Moderators";
$lang['spaces-contributors'] = "Contributors";
$lang['spaces-who_can_post'] = "Who can post";
$lang['spaces-who_can_post-all'] = "Anyone can post";
$lang['spaces-who_can_post-contributors'] = "Only contributors can post";
$lang['admin-reports-type-space'] = "Space";
$lang['admin-general-reg-social'] = "Allow social login";
$lang['admin-general-posting-spaces_classifications'] = "Spaces classifications";
$lang['notif-report-s_publisher-approve-title'] = "Content removed based on users reports";
$lang['notif-report-s_publisher-approve-msg'] = "Your space ([TITLE]) removed from website based on users reports, please stick to our <a href='{$url_mapper['pages/view']}terms'>Terms and Conditions</a> in your future posts";
$lang['notif-report-s_reporter-approve-msg'] = "Space ([TITLE]) removed from website based on your report, thanks for reporting";
$lang['notif-report-s_reporter-reject-title'] = "Report reviewd, Content not removed";
$lang['notif-report-s_reporter-reject-msg'] = "Your report about the space ([TITLE]) was reviewd and it doesnt violate our <a href='{$url_mapper['pages/view']}terms'>Terms and Conditions</a>, so it wont be removed from website";