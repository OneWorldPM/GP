<?php

/**
 * Never use same name for two different instances of the apps or two different apps
 * In Socket IO backend, I uses user id from php app to match many things
 * So user id might be same for different users on different apps
 *
 * -Athul AK
 */

$config = array(
    "socket_app_name" => "GP", //eg; cco_dev_athul
    "socket_lounge_room" => "GP_LOUNGE_GROUP", //eg; cco_lounge_group_dev_athul
    "socket_lounge_oto_chat_group" => "GP_LOUNGE_OTO", //eg; cco_lounge_oto_dev_athul
    "socket_active_user_list" => "GP_ACTIVE_USERS", //eg; cco_active_users_dev_athul
    "socket_lounge_video_meet_room" => "GP_LOUNGE_VIDEO_MEET", //eg; cco_lounge_video_meet_dev_your_name
    "socket_lounge_oto_video" => "GP_LOUNGE_OTO_VIDEO", //eg; cco_lounge_video_meet_dev_your_name
    "socket_roundtable_room" => "GP_ROUNDTABLE_ROOM", //eg; cco_lounge_video_meet_dev_your_name
    "socket_roundtable_additional_room" => "GP_ROUNDTABLE_ADDITIONAL_ROOM", //eg; cco_lounge_video_meet_dev_your_name
    "socket_breakout_room" => "GP_BREAKOUT_ROOM", //eg; cco_lounge_video_meet_dev_your_name
    "socket_breakout_additional_room" => "GP_BREAKOUT_ADDITIONAL_ROOM" //eg; cco_lounge_video_meet_dev_your_name
);

echo json_encode($config);
