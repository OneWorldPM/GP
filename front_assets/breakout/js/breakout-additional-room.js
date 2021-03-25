var localVideo;
var firstPerson = false;
var socketCount = 0;
var socketId;
var localStream;
var connections = [];

var ROUND_TABLE = socket_roundtable_additional_room+'_'+round_table_id;
var breakout_room_name = socket_breakout_additional_room+'_'+round_table_id;

var peerConnectionConfig = {
    'iceServers': [
        { 'urls': 'stun:stun.l.google.com:19302' },
        { 'urls': 'stun:stun1.l.google.com:19302' },
        {
            url: 'turn:numb.viagenie.ca',
            credential: 'muazkh',
            username: 'webrtc@live.com'
        },
        {
            url: 'turn:192.158.29.39:3478?transport=udp',
            credential: 'JZEOEt2V3Qb0y27GRntt2u2PAYA=',
            username: '28224511:1379330808'
        }
    ]
};



$('.leave-btn').on('click', function () {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to leave the meeting but you can always come back!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, leave!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.replace(base_url+"/sessions");
            // if(!window.top.close())
            // {
            //     Swal.fire(
            //         'Problem!',
            //         "Since you didn't open this meeting tab from our app, we are unable to automatically make you leave but you can simply close this browser tab and you will leave the meeting!",
            //         'error'
            //     )
            // }
        }
    });
});

function pageReady()
{
    $('.localvideo-div').hide();
    let checkCam = () => navigator.mediaDevices.getUserMedia({ video: true, audio: true })
        .then((stream) => {
            // User allowed camera and mic access

            $('.localvideo-div').show();
            everythingReady();
            return true;
        })
        .catch((log)=>{
            // User blocked camera and mic access

            $('#camera-container').html('' +
                '<div class="col-md-12 text-center"><span style="color: red;">You need to allow camera and microphone access and reload to join this meeting</span></div>' +
                '<div class="col-md-12 text-center"><button class="btn btn-success" onclick="location.reload();">Reload</button></div>');
            Swal.fire(
                'Problem!',
                'You need to allow camera and microphone access and reload to join this meeting',
                'error'
            );
            return false;
        });

    checkCam();
}

function everythingReady() {

    $("html").on("contextmenu",function(){
        return false;
    });

    localVideo = document.getElementById('localVideo');

    var constraints = {
        video: true,
        audio: true,
    };

    if(navigator.mediaDevices.getUserMedia) {

        navigator.mediaDevices.getUserMedia(constraints)
            .then(getUserMediaSuccess)
            .then(function(i){

                socket.emit('joinRoundTable', ROUND_TABLE, atttendee_name, attendee_id, 'cam', isHost);
                socket.on('signal', gotMessageFromServer);

                socket.on('joinRoundTable', function(){
                    console.log('connected with id:'+socket.id);

                    socketId = socket.id;

                    socket.on('user-left-roundtable', function(id){
                        var video = document.querySelector('[data-socket="'+ id +'"]');
                        if(video == null)
                            return;
                        var parentDiv = video.parentElement;
                        video.parentElement.remove();
                        Dish();
                    });


                    socket.on('user-joined-roundtable', function(id, count, clients, table, attendees_list){
                        if (table != ROUND_TABLE)
                            return;
                        clients.forEach(function(socketListId) {
                            if(!connections[socketListId]){
                                connections[socketListId] = new RTCPeerConnection(peerConnectionConfig);
                                //Wait for their ice candidate
                                connections[socketListId].onicecandidate = function(event){
                                    if(event.candidate != null) {
                                        console.log('SENDING ICE');
                                        socket.emit('signal', socketListId, JSON.stringify({'ice': event.candidate}));
                                    }
                                }

                                //Wait for their video stream
                                connections[socketListId].onaddstream = function(event){
                                    gotRemoteStream(event, socketListId, attendees_list[socketListId])
                                }

                                //Add the local video stream
                                connections[socketListId].addStream(localStream);
                            }
                        });

                        //Create an offer to connect with your local description

                        if(count >= 2){
                            connections[id].createOffer().then(function(description){
                                connections[id].setLocalDescription(description).then(function() {
                                    // console.log(connections);
                                    socket.emit('signal', id, JSON.stringify({'sdp': connections[id].localDescription}));
                                }).catch(e => console.log(e));
                            });
                        }
                    });
                })

                // Muting functionality
                $('.mute-mic-btn').on('click', function () {

                    if ($('#muteStatus').val() == 'unmuted')
                    {
                        $('#muteStatus').val('muted');
                        socket.emit('mute-me', ROUND_TABLE);
                        $('.mute-mic-btn').removeClass('btn-success');
                        $('.mute-mic-btn').addClass('btn-danger');
                        $('.mute-mic-btn').html('<i class="fa fa-microphone-slash fa-2x" aria-hidden="true"></i> Unmute');
                        $('.my-muteIndicator-icon').html('<i class="fa fa-microphone-slash fa-2x" aria-hidden="true" style="color: #ff422b"></i>');

                        let nameTagText = (isHost)?'You[host] (Muted)':'You (Muted)';
                        $('.localVideoParent > .name-tag').text(nameTagText);
                    }else{
                        $('#muteStatus').val('unmuted');
                        socket.emit('unmute-me', ROUND_TABLE);
                        $('.mute-mic-btn').removeClass('btn-danger');
                        $('.mute-mic-btn').addClass('btn-success');
                        $('.mute-mic-btn').html('<i class="fa fa-microphone fa-2x" aria-hidden="true"></i> Mute');
                        $('.my-muteIndicator-icon').html('<i class="fa fa-microphone fa-2x" aria-hidden="true" style="color: #12b81c"></i>');
                        let nameTagText = (isHost)?'You[host]':'You';
                        $('.localVideoParent > .name-tag').text(nameTagText);
                    }
                });
                socket.on('mute-me', function(user_socket){
                    console.log('mute:'+user_socket);
                    $('video[data-socket="'+user_socket+'"]').prop('muted', true);
                    $('.muteIndicator-icon[data-socket="'+user_socket+'"]').css('display', '');
                });

                socket.on('unmute-me', function(user_socket){
                    $('video[data-socket="'+user_socket+'"]').prop('muted', false);
                    $('.muteIndicator-icon[data-socket="'+user_socket+'"]').css('display', 'none');
                });
                // End of muting functionality

                // Turn off/on cam functionality
                $('.cam-btn').on('click', function () {
                    if ($('#camStatus').val() == 'on')
                    {
                        $('#camStatus').val('off');
                        socket.emit('cam-off', ROUND_TABLE);
                        $('.cam-btn').removeClass('btn-success');
                        $('.cam-btn').addClass('btn-danger');
                        $('.cam-btn').html('<i class="fa fa-video-camera fa-2x" aria-hidden="true"></i> Start Video');

                        $('.localVideoParent').css('background', "black");
                        $('#localVideo').css('display', 'none');
                    }else{
                        $('#camStatus').val('on');
                        socket.emit('cam-on', ROUND_TABLE);
                        $('.cam-btn').removeClass('btn-danger');
                        $('.cam-btn').addClass('btn-success');
                        $('.cam-btn').html('<i class="fa fa-video-camera fa-2x" aria-hidden="true"></i> Stop Video');
                        $('#localVideo').css('display', '');
                    }
                });
                socket.on('cam-off', function(user_socket){
                    $('video[data-socket="'+user_socket+'"]').parent().css('background', 'black');
                    $('video[data-socket="'+user_socket+'"]').css('display', 'none');
                });

                socket.on('cam-on', function(user_socket){
                    $('video[data-socket="'+user_socket+'"]').css('display', '');
                });
                // End of turn off/on cam functionality

            });

    } else {
        alert('Your browser does not support getUserMedia API');
    }
}

function getUserMediaSuccess(stream) {

    localStream = stream;

    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
    navigator.getUserMedia({
        'audio': false,
        'video': true
    }, function (stream) {
        localVideo.srcObject = stream;

        setTimeout(function () {
            $('.control-icons-col').css('display', '');
        }, 5000);
    }, logError);

    $('.localvideo-div').prepend('<span class="my-muteIndicator-icon" ><i class="fa fa-microphone fa-2x" aria-hidden="true" style="color: #12b81c"></i></span>');
}

function gotRemoteStream(event, id, attendee) {

    if(id == socketId)
        return;

    let Scenary = document.getElementById('Dish');
    let Camera = document.createElement('div');

    let muteIndicator = document.createElement('span');
    muteIndicator.setAttribute('class', 'muteIndicator-icon');
    muteIndicator.setAttribute('data-socket', id);
    muteIndicator.style.display = (attendee.muteStatus == 'muted')?'':'none';
    muteIndicator.innerHTML = ' <i class="fa fa-microphone-slash" aria-hidden="true" style="color: red"></i>';


    let nameTag = document.createElement('span');
    nameTag.setAttribute('class', 'name-tag');
    let hostText = (attendee.isHost)?' [host] ':'';

    if (attendee.sharingType == 'screen'){
        nameTag.innerHTML = attendee.name + hostText + ' (Screen)';
    }else{
        nameTag.innerHTML = attendee.name + hostText;
        nameTag.appendChild(muteIndicator);
    }

    let fullscreenBtn = document.createElement('span');
    fullscreenBtn.setAttribute('class', 'fullscreen-btn');
    fullscreenBtn.setAttribute('title', 'Full screen');
    fullscreenBtn.innerHTML = '<i class="fa fa-arrows" aria-hidden="true" style="border: 1px solid;"></i>';

    let video = document.createElement('video');
    video.setAttribute('data-socket', id);
    video.setAttribute('width', '100%');
    video.style.display = (attendee.camStatus == 'off')?'none':'';
    video.srcObject   = event.stream;
    video.autoplay    = true;
    if (attendee.muteStatus == 'muted')
        video.muted = true;
    video.playsinline = true;

    if(attendee.camStatus == 'off')
        Camera.style.background = 'black';
    Camera.className = 'Camera';
    Camera.appendChild(nameTag);
    Camera.appendChild(fullscreenBtn);
    Camera.appendChild(video);
    Scenary.appendChild(Camera);
    Dish();

    // var videos = document.querySelectorAll('camera-feeds'),
    //     div    = document.createElement('div'),
    //     nameTag = document.createElement('span'),
    //     fullscreenBtn = document.createElement('span'),
    //     muteIndicator = document.createElement('span'),
    //     videoCover = document.createElement('div'),
    //     videoParent = document.createElement('div')
    //     ;
    //
    // div.setAttribute('class', 'col-md-3');
    //
    // nameTag.setAttribute('class', 'name-tag');
    // if (attendee.sharingType == 'screen'){
    //     nameTag.innerHTML = attendee.name+' (Screen)';
    // }else{
    //     nameTag.innerHTML = attendee.name;
    // }
    //
    // videoCover.setAttribute('class', 'videoCover');
    // videoCover.setAttribute('data-socket', id);
    // videoCover.style.display = (attendee.camStatus == 'off')?'':'none';
    //
    // fullscreenBtn.setAttribute('class', 'fullscreen-btn');
    // fullscreenBtn.innerHTML = '<i class="fa fa-arrows" aria-hidden="true" style="border: 1px solid;"></i>';
    //
    // muteIndicator.setAttribute('class', 'muteIndicator-icon');
    // muteIndicator.setAttribute('data-socket', id);
    // muteIndicator.style.display = (attendee.muteStatus == 'muted')?'':'none';
    // muteIndicator.innerHTML = '<i class="fa fa-microphone-slash fa-2x" aria-hidden="true" style="color: red"></i>';
    //
    // video.setAttribute('data-socket', id);
    // video.setAttribute('width', '100%');
    // video.srcObject   = event.stream;
    // video.autoplay    = true;
    // if (attendee.muteStatus == 'muted')
    //     video.muted = true;
    // video.playsinline = true;
    //
    // videoParent.style.width = '100%';
    // videoParent.appendChild(videoCover);
    // videoParent.appendChild(video);
    //
    //
    // //div.appendChild(videoCover);
    // div.appendChild(nameTag);
    // div.appendChild(fullscreenBtn);
    // div.appendChild(muteIndicator);
    //
    //
    //
    // div.appendChild(videoParent);
    // document.querySelector('.camera-feeds').prepend(div);
}

function gotMessageFromServer(fromId, message) {

    //Parse the incoming signal
    var signal = JSON.parse(message)

    //Make sure it's not coming from yourself
    if(fromId != socketId) {

        if(signal.sdp){
            connections[fromId].setRemoteDescription(new RTCSessionDescription(signal.sdp)).then(function() {
                if(signal.sdp.type == 'offer') {
                    connections[fromId].createAnswer().then(function(description){
                        connections[fromId].setLocalDescription(description).then(function() {
                            socket.emit('signal', fromId, JSON.stringify({'sdp': connections[fromId].localDescription}));
                        }).catch(e => console.log(e));
                    }).catch(e => console.log(e));
                }
            }).catch(e => console.log(e));
        }

        if(signal.ice) {
            connections[fromId].addIceCandidate(new RTCIceCandidate(signal.ice)).catch(e => console.log(e));
        }
    }
}

function logError(error) {
    displaySignalMessage(error.name + ': ' + error.message);
}

$('#camera-container').on('click', 'span.fullscreen-btn', function () {

    // if already full screen; exit
    // else go fullscreen
    if (
        document.fullscreenElement ||
        document.webkitFullscreenElement ||
        document.mozFullScreenElement ||
        document.msFullscreenElement
    ) {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }

        $(this).parent().children('.fullscreen-btn-fullscreen').eq(0).html('<i class="fa fa-arrows" aria-hidden="true" style="border: 1px solid;"></i>');
        $(this).parent().children('.fullscreen-btn-fullscreen').eq(0).removeClass('nametag-fullscreen');

    } else {
        element = $(this).parent().get(0);
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }

        $(this).parent().children('.name-tag').eq(0).addClass('nametag-fullscreen');

        $(this).parent().children('.fullscreen-btn').eq(0).addClass('fullscreen-btn-fullscreen');
        $(this).parent().children('.fullscreen-btn').eq(0).html('<i class="fa fa-compress fa-2x" aria-hidden="true" style="border: 1px solid;"></i>');

    }
});

$(document).bind('fullscreenchange webkitfullscreenchange mozfullscreenchange msfullscreenchange', function (e) {
    var fullscreenElement = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullscreenElement || document.msFullscreenElement;

    if (!fullscreenElement) {
        //console.log('Leaving full-screen mode...');
        $('.nametag-fullscreen').removeClass('nametag-fullscreen');

        $('.fullscreen-btn-fullscreen').html('<i class="fa fa-arrows" aria-hidden="true" style="border: 1px solid;"></i>');
        $('.fullscreen-btn-fullscreen').removeClass('fullscreen-btn-fullscreen');
    } else {
        //console.log('Entering full-screen mode...');
    }
});


/********** Breakout room features *************/
let participants;
let waiting;
let roomData;
let participantsAllocation;
let roomsCreated = false;

$(document).ready(function () {

});

function participantsList() {
    socket.on('breakoutRoomParticipantsListChange', function () {
        socket.emit('getBreakoutRoomParticipantsList', breakout_room_name);
    });

    // Update participants list
    socket.on('breakoutRoomRoomParticipantsList', function (users) {
        participants = (users==null || Object.keys(users).length == 0)?{}:users;
        if (users == null || Object.keys(users).length == 0)
        {
            $('#participantsListCount').text(0);
            $('#participantsList').html('');
            $('#participantsList').append('<li class="list-group-item">Empty</li>');
            return false;
        }

        $('#participantsListCount').text(Object.keys(users).length);
        $('#participantsList').html('');
        $.each(users, function( socket, details ) {
            let removeBtn;
            let hostText;
            if (details.isHost == true)
            {
                hostText = '(host)';
                removeBtn = '';
            }else{
                hostText = '';
                if (isHost)
                    removeBtn = '<span class="badge removeUser" user-name="'+details.name+'" user-id="'+details.id+'" socket-id="'+socket+'"><i class="far fa-times-circle"></i> Remove</span>';
                else
                    removeBtn = '';
            }
            $('#participantsList').append('<li class="list-group-item">'+details.name+' '+hostText+' '+removeBtn+'</li>');
        });
    });
}

function joinParticipantsList() {
    socket.emit('joinBreakoutRoomParticipantsList', breakout_room_name, atttendee_name, attendee_id, isHost);

}

$('.breakout-rooms-btn').on('click', function () {

    Swal.fire({
        title: 'Please Wait',
        text: 'Loading room details...',
        imageUrl: base_url+'front_assets/breakout/cam-grids/ycl_anime_500kb.gif',
        imageAlt: 'Loading...',
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false
    });

    fillCreatedBreakoutRooms(showRoomCreateOptions());

});

function showRoomCreateOptions()
{
    $('#createdRoomsDiv').hide();

    Swal.fire({
        title: 'Please Wait',
        text: 'Doing the math...',
        imageUrl: base_url+'front_assets/breakout/cam-grids/ycl_anime_500kb.gif',
        imageAlt: 'Loading...',
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false
    });

    if (Object.keys(participants).length > 2)
    {
        $('#participantsCountForRooms').text((Object.keys(participants).length)-1);
        $('#numberOfRoomsSelect').html('');
        for (let i = 2; i < (Object.keys(participants).length); i++)
        {
            $('#numberOfRoomsSelect').append('<option value="'+i+'">'+i+'</option>');
        }

        let participantsOtherThanHost = (Object.keys(participants).length)-1;
        let participantsPerRoomText = ((participantsOtherThanHost % 2 !== 0)?'&asymp; '+Math.round(participantsOtherThanHost/2):(participantsOtherThanHost/2))+' participants per room';

        $('#notEnoughParticipantsMsg').hide();
        $('#participantPerRoomInfo').html(participantsPerRoomText);
        $('#participantPerRoomInfo').show();
        $('.create-room-btn').prop('disabled', false);
        $('#createRoomsDiv').show();

        Swal.close();

    }else{
        $('#createRoomsDiv').hide();
        $('#participantPerRoomInfo').hide();
        $('.create-room-btn').prop('disabled', true);
        $('#notEnoughParticipantsMsg').show();

        Swal.close();
    }

    $('#breakoutRoomsModal').modal('show');
}

$('#numberOfRoomsSelect').on('change', function () {
    let rooms = $(this).val();
    let participantsOtherThanHost = (Object.keys(participants).length)-1;
    let participantsPerRoomText = ((participantsOtherThanHost % rooms !== 0)?'&asymp; '+Math.round(participantsOtherThanHost/rooms):(participantsOtherThanHost/rooms))+' participants per room';
    $('#participantPerRoomInfo').html(participantsPerRoomText);
});

$('.create-room-btn').on('click', function () {
    createBreakOutRooms();
});

function createBreakOutRooms()
{
    Swal.fire({
        title: 'Please Wait',
        text: 'Creating rooms...',
        imageUrl: base_url+'front_assets/breakout/cam-grids/ycl_anime_500kb.gif',
        imageAlt: 'Loading...',
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false
    });

    let participantsOtherThanHost = (Object.keys(participants).length)-1;

    if (participantsOtherThanHost < 2)
    {
        $('#createRoomsDiv').hide();
        $('#participantPerRoomInfo').hide();
        $('.create-room-btn').prop('disabled', true);
        $('#notEnoughParticipantsMsg').show();
        Swal.fire(
            'Unable to create rooms',
            'You need at least 2 participants other than the host to create rooms.',
            'error'
        );

        return false;
    }

    let rooms = $('#numberOfRoomsSelect').val();
    let method = $("input[type='radio'][name='roomCreationType']:checked").val();

    if (method == 'automatic')
    {
        $.post(base_url+"breakout_room/createRoomsAutomatic",
            {
                session_id: round_table_id,
                hostId: attendee_id,
                rooms: rooms,
                participants: participants
            })
            .done(function( data ) {
                data = JSON.parse(data);
                if (data.status == 'success')
                {
                    Swal.fire(
                        'Done!',
                        data.msg,
                        'success'
                    );

                    fillCreatedBreakoutRooms();

                }else{
                    Swal.fire(
                        'Unable to create rooms',
                        data.msg,
                        'error'
                    );
                }
            })
            .fail(()=>{
                Swal.fire(
                    'Network error',
                    'Unable to create rooms',
                    'error'
                );
            });

    }else{
        Swal.close();
        toastr.warning('Manual creation of rooms are not yet supported');
    }
}

let alreadyAddedParticipants = [];
let participantsItemNotInAnyRoom;
function fillCreatedBreakoutRooms(callback)
{
    Swal.fire({
        title: 'Please Wait',
        text: 'Loading room details...',
        imageUrl: base_url+'front_assets/breakout/cam-grids/ycl_anime_500kb.gif',
        imageAlt: 'Loading...',
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false
    });

    let participantsOtherThanHost = (Object.keys(participants).length)-1;

    if (participantsOtherThanHost < 2)
    {
        $('#createRoomsDiv').hide();
        $('#participantPerRoomInfo').hide();
        $('.create-room-btn').prop('disabled', true);
        $('#notEnoughParticipantsMsg').show();
        Swal.fire(
            'Unable to create rooms',
            'You need at least 2 participants other than the host to create rooms.',
            'error'
        );

        callback;
        return false;
    }

    $.get(base_url+"breakout_room/createdRoomDetails/"+breakout_meeting_id, function (rooms) {
        rooms = JSON.parse(rooms);

        if($.isEmptyObject(rooms))
        {
           Swal.close();
            callback;
            return false;
        }

        $('#createRoomsDiv').hide();
        $('#participantPerRoomInfo').hide();
        $('#notEnoughParticipantsMsg').hide();

        $('.create-room-btn').addClass('open-room-btn');
        $('.create-room-btn').css('background', '#38aa44');
        $('.create-room-btn').text('Open All Rooms');
        $('.create-room-btn').prop('disabled', false);
        $('.create-room-btn').removeClass('create-room-btn');

        $('#automaticRoomsDiv').html('');
        $.each(rooms, function(i, room) {

            let participantsItem = '';
            $.each(room.participants, function(i, participant) {
                alreadyAddedParticipants.push(participant.user_id);
                participantsItem += '<span class="participantInRoom badge" participant-id="'+participant.id+'" user-id="'+participant.user_id+'"><i class="fas fa-user"></i> '+participant.name+'</span>';
            });

            $('#automaticRoomsDiv').append(
                '<span class="createdRoomLabel">'+room.name+'</span>\n' +
                '<div class="createdRoom" room-id="'+room.id+'">\n' +
                    participantsItem +
                '</div>'
            );
        });

        $.each(participants, function( socket, participant )
        {
            if(!alreadyAddedParticipants.includes(participant.id) && participant.id != attendee_id)
            {
                participantsItemNotInAnyRoom += '<span class="participantInRoom badge" user-id="'+participant.id+'"><i class="fas fa-user"></i> '+participant.name+'</span>';
            }
        });
        $('.mainRoom').html('');
        $('.mainRoom').append('<span class="hostInRoom badge" user-id="'+attendee_id+'"><i class="fas fa-user" aria-hidden="true"></i> You</span>');
        $('.mainRoom').append(participantsItemNotInAnyRoom);


        $( ".createdRoom, .mainRoom" ).sortable({
            cancel: ".hostInRoom",
            connectWith: $('.createdRoom, .mainRoom'),
            receive: function(event, participantData) { // Dropped to a room
                /**
                 * @type {*|jQuery}
                 * Required objects
                 */
                let participant = $(participantData.item).first(); // Who
                let toRoom = $(this).first(); // To where
                let fromRoom = $(participantData.sender).first(); // From where

                /**
                 * Object params
                 */
                let participantId = participant.attr('participant-id');
                let participantUserId = participant.attr('user-id');
                let fromRoomId = fromRoom.attr('room-id');
                let toRoomId = toRoom.attr('room-id');

                if (toRoomId == 'main' && fromRoomId != 'main') // Delete from room and move to main room
                {

                    Swal.fire({
                        title: 'Please Wait',
                        text: 'Moving the participant...',
                        imageUrl: base_url+'front_assets/breakout/cam-grids/ycl_anime_500kb.gif',
                        imageAlt: 'Loading...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    $.post(base_url+"breakout_room/removeParticipantFromRoom",
                        {
                            participant_id: participantId
                        })
                        .done(function(data)
                        {
                            data = JSON.parse(data);
                            if(data.status =='success')
                            {
                                Swal.close();
                                console.log('done');
                            }else{
                                Swal.fire(
                                    'Error',
                                    'Unable to move participant',
                                    'error'
                                );
                                participantData.sender.sortable('cancel');
                            }
                        })
                        .fail(()=>
                        {
                            Swal.fire(
                                'Error',
                                'Unable to move participant',
                                'error'
                            );
                            participantData.sender.sortable('cancel');
                        });

                }else if(fromRoomId == 'main') // Add to dropped room
                {
                    Swal.fire({
                        title: 'Please Wait',
                        text: 'Moving the participant...',
                        imageUrl: base_url+'front_assets/breakout/cam-grids/ycl_anime_500kb.gif',
                        imageAlt: 'Loading...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    $.post(base_url+"breakout_room/addParticipantToRoom",
                        {
                            user_id: participantUserId,
                            to_room_id: toRoomId
                        })
                        .done(function(data)
                        {
                            data = JSON.parse(data);
                            if(data.status =='success')
                            {
                                participant.attr('participant-id', data.participant_id);
                                Swal.close();
                                console.log('done');
                            }else{
                                Swal.fire(
                                    'Error',
                                    'Unable to move participant',
                                    'error'
                                );
                                participantData.sender.sortable('cancel');
                            }
                        })
                        .fail(()=>
                        {
                            Swal.fire(
                                'Error',
                                'Unable to move participant',
                                'error'
                            );
                            participantData.sender.sortable('cancel');
                        });

                }else if(fromRoomId != toRoomId) // update current room data
                {
                    Swal.fire({
                        title: 'Please Wait',
                        text: 'Moving the participant...',
                        imageUrl: base_url+'front_assets/breakout/cam-grids/ycl_anime_500kb.gif',
                        imageAlt: 'Loading...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    $.post(base_url+"breakout_room/changeParticipantRoom",
                        {
                            participant_id: participantId,
                            to_room_id: toRoomId
                        })
                        .done(function(data)
                        {
                            data = JSON.parse(data);
                            if(data.status =='success')
                            {
                                Swal.close();
                                console.log('done');
                            }else{
                                Swal.fire(
                                    'Error',
                                    'Unable to move participant',
                                    'error'
                                );
                                participantData.sender.sortable('cancel');
                            }
                        })
                        .fail(()=>
                        {
                            Swal.fire(
                                'Error',
                                'Unable to move participant',
                                'error'
                            );
                            participantData.sender.sortable('cancel');
                        });
                }
            }
        });

        participantsItemNotInAnyRoom = '';
        alreadyAddedParticipants = [];

        $('#createdRoomsDiv').show();

        $('#breakoutRoomsModal').modal('show');

        Swal.close();
        return true;

    }).fail(()=>{
        $('#breakoutRoomsModal').modal('hide');
        Swal.fire(
            'Network Error',
            'Unable to verify room data.',
            'error'
        );
        callback;
        return false;
    });
}

function redirectToMainRoom()
{
    socket.on('redirectToMainBreakoutRoom', function(meetingId)
    {
        if (meetingId == main_meeting_id)
            window.location.replace(base_url+"breakout_room/meeting/"+meetingId);
    });
}
