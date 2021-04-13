var localVideo;
var firstPerson = false;
var socketCount = 0;
var socketId;
var localStream;
var connections = [];

var config = {'host': 'https://socket.yourconference.live'};

var MEETING_ROOM = socket_roundtable_room+'_'+meeting_id;

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

$(function() {

    $("html").on("contextmenu",function(){
        return false;
    });

    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };


    localVideo = document.getElementById('localVideo');

    $('.sharescreen-btn').on('click', function () {

        if ($('.screenshare-status').val() == 'sharing')
        {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to stop sharing the screen, but your camera still might be broadcasting on the other tab!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.top.close();
                }
            });
        }else{
            var constraints = {
                video: true
            };

            if(navigator.mediaDevices.getDisplayMedia) {
                socket = io.connect(config.host, {secure: true});
                navigator.mediaDevices.getDisplayMedia(constraints)
                    .then(getUserMediaSuccess)
                    .then(function(){
                        socket.emit('joinRoundTable', MEETING_ROOM, user_name, user_id, 'screen');
                        socket.on('signal', gotMessageFromServer);

                        socket.on('joinRoundTable', function(){
                            console.log('connected with id:'+socket.id);

                            socketId = socket.id;

                            socket.on('user-left-roundtable', function(id){
                                var video = document.querySelector('[data-socket="'+ id +'"]');
                                if(video == null)
                                    return;
                                var parentDiv = video.parentElement;
                                video.parentElement.parentElement.removeChild(parentDiv);
                            });


                            socket.on('user-joined-roundtable', function(id, count, clients, table, attendees_list){
                                if (table != MEETING_ROOM)
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

                    });

            } else {
                alert('Your browser does not support getUserMedia API');
            }
        }
    });

});

function getUserMediaSuccess(stream) {
    localStream = stream;


    $('.sharescreen-btn').removeClass('btn-success');
    $('.sharescreen-btn').addClass('btn-danger');
    $('.sharescreen-btn').text('Stop Sharing Screen');

    $('.screenshare-status').val('sharing');

    // navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
    // navigator.getUserMedia({
    //     'audio': false,
    //     'video': true
    // }, function (stream) {
    //     localVideo.srcObject = stream;
    // }, logError);
}

function gotRemoteStream(event, id, attendee) {
    return;

    if(id == socketId)
        return;

    var videos = document.querySelectorAll('camera-feeds'),
        video  = document.createElement('video'),
        div    = document.createElement('div'),
        nameTag = document.createElement('span'),
        fullscreenBtn = document.createElement('span'),
        muteIndicator = document.createElement('span');

    div.setAttribute('class', 'col-md-3');

    nameTag.setAttribute('class', 'name-tag');
    nameTag.innerHTML = attendee['name'];

    fullscreenBtn.setAttribute('class', 'fullscreen-btn');
    fullscreenBtn.innerHTML = '<i class="fa fa-arrows" aria-hidden="true" style="border: 1px solid;"></i>';

    muteIndicator.setAttribute('class', 'muteIndicator-icon');
    muteIndicator.setAttribute('data-socket', id);
    muteIndicator.style.display = (attendee.muteStatus == 'muted')?'':'none';
    muteIndicator.innerHTML = '<i class="fa fa-microphone-slash fa-2x" aria-hidden="true" style="color: red"></i>';

    video.setAttribute('data-socket', id);
    video.setAttribute('width', '100%');
    video.srcObject   = event.stream;
    video.autoplay    = true;
    if (attendee.muteStatus == 'muted')
        video.muted       = true;
    video.playsinline = true;

    div.appendChild(video);
    div.appendChild(nameTag);
    div.appendChild(fullscreenBtn);
    div.appendChild(muteIndicator);
    document.querySelector('.camera-feeds').prepend(div);
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
