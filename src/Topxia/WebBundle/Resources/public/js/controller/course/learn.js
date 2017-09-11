define(function(require, exports, module) {
    var Widget = require('widget'),
        Class = require('class'),
        Store = require('store'),
        Backbone = require('backbone'),
        swfobject = require('swfobject'),
        Scrollbar = require('jquery.perfect-scrollbar'),
        Notify = require('common/bootstrap-notify');
        chapterAnimate = require('../course/widget/chapter-animate');
        var Messenger = require('../player/messenger');

        require('mediaelementplayer');

    var Toolbar = require('../lesson/lesson-toolbar');

    var SlidePlayer = require('../widget/slider-player');
    var DocumentPlayer = require('../widget/document-player');

    var iID = null;

    var LessonDashboard = Widget.extend({

        _router: null,

        _toolbar: null,

        _lessons: [],

        _counter: null,

        events: {
            'click [data-role=next-lesson]': 'onNextLesson',
            'click [data-role=prev-lesson]': 'onPrevLesson',
            'click [data-role=finish-lesson]': 'onFinishLesson'
        },

        attrs: {
            courseId: null,
            courseUri: null,
            dashboardUri: null,
            lessonId: null,
            watchLimit: false
        },

        setup: function() {
            this._readAttrsFromData();
            this._initToolbar();
            this._initRouter();
            this._initListeners();
            this._initChapter();

            $('.prev-lesson-btn, .next-lesson-btn').tooltip();
        },

        onNextLesson: function(e) {
            var next = this._getNextLessonId();
            if (next > 0) {
                this._router.navigate('lesson/' + next, {trigger: true});
            }
        },

        onPrevLesson: function(e) {
            var prev = this._getPrevLessonId();
            if (prev > 0) {
                this._router.navigate('lesson/' + prev, {trigger: true});
            }
        },

        onFinishLesson: function(e) {
            var $btn = this.element.find('[data-role=finish-lesson]');
            if ($btn.hasClass('btn-success')) {
                this._onCancelLearnLesson();
            } else {
                this._onFinishLearnLesson();
            }
        },

        _startLesson: function() {
            var toolbar = this._toolbar,
                self = this;
            var url = '../../course/' + this.get('courseId') + '/lesson/' + this.get('lessonId') + '/learn/start';
            $.post(url, function(result) {
                if (result == true) {
                    toolbar.trigger('learnStatusChange', {lessonId:self.get('lessonId'), status: 'learning'});
                }
            }, 'json');
        },

        _onFinishLearnLesson: function() {
            var $btn = this.element.find('[data-role=finish-lesson]'),
            toolbar = this._toolbar,
            self = this;

            var url = '../../course/' + this.get('courseId') + '/lesson/' + this.get('lessonId') + '/learn/finish';
            $.post(url, function(response) {
                if (response.isLearned) {
                    $('#course-learned-modal').modal('show');
                }

                $btn.addClass('btn-success');
                $btn.find('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
                toolbar.trigger('learnStatusChange', {lessonId:self.get('lessonId'), status: 'finished'});

            }, 'json');

        },

        _onCancelLearnLesson: function() {
            var $btn = this.element.find('[data-role=finish-lesson]'),
                toolbar = this._toolbar,
                self = this;
            var url = '../../course/' + this.get('courseId') + '/lesson/' + this.get('lessonId') + '/learn/cancel';
            $.post(url, function(json) {
                $btn.removeClass('btn-success');
                $btn.find('.glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
                toolbar.trigger('learnStatusChange', {lessonId:self.get('lessonId'), status: 'learning'});
            }, 'json');
        },

        _readAttrsFromData: function() {
            this.set('courseId', this.element.data('courseId'));
            this.set('courseUri', this.element.data('courseUri'));
            this.set('dashboardUri', this.element.data('dashboardUri'));
            this.set('watchLimit', this.element.data('watchLimit'));

        },

        _initToolbar: function() {
            this._toolbar = new Toolbar({
                element: '#lesson-dashboard-toolbar',
                activePlugins:  app.arguments.plugins,
                courseId: this.get('courseId')
            }).render();

            $('#lesson-toolbar-primary li[data-plugin=lesson]').trigger('click');
        },

        _initRouter: function() {
            var that = this,
                DashboardRouter = Backbone.Router.extend({
                routes: {
                    "lesson/:id": "lessonShow"
                },

                lessonShow: function(id) {
                    that.set('lessonId', id);
                }
            });

            this._router = new DashboardRouter();
            Backbone.history.start({pushState: false, root:this.get('dashboardUri')} );
        },

        _initListeners: function() {
            var that = this;
            this._toolbar.on('lessons_ready', function(lessons){
                that._lessons = lessons;
                that._showOrHideNavBtn();
                
                if ($('.es-wrap [data-toggle="tooltip"]').length > 0) {
                    $('.es-wrap [data-toggle="tooltip"]').tooltip({container: 'body'});
                }
            });
        },

        _afterLoadLesson: function(lessonId) {
            if (this._counter && this._counter.timerId) {
                clearInterval(this._counter.timerId);
            }

            var self = this;
            this._counter = new Counter(self, this.get('courseId'), lessonId, this.get('watchLimit'));
            this._counter.setTimerId(setInterval(function(){self._counter.execute()}, 1000));
        },

        _onChangeLessonId: function(id) {
            var self = this;
            if (!this._toolbar) {
                return ;
            }
            this._toolbar.set('lessonId', id);

            swfobject.removeSWF('lesson-swf-player');

            $('#lesson-iframe-content').empty();
            $('#lesson-video-content').html("");

            this.element.find('[data-role=lesson-content]').hide();

            var that = this;
            $.get(this.get('courseUri') + '/lesson/' + id, function(lesson) {
                
                that.element.find('[data-role=lesson-title]').html(lesson.title);

                that.element.find('[data-role=lesson-title]').html(lesson.title);
                $(".watermarkEmbedded").html('<input type="hidden" id="videoWatermarkEmbedded" value="'+lesson.videoWatermarkEmbedded+'" />');
                var $titleStr = "";
                $titleArray = document.title.split(' - ');
                $.each($titleArray,function(key,val){
                    $titleStr += val + ' - ';
                })
                document.title = lesson.title + ' - ' + $titleStr.substr(0,$titleStr.length-3);
                that.element.find('[data-role=lesson-number]').html(lesson.number);
                if (parseInt(lesson.chapterNumber) > 0) {
                    that.element.find('[data-role=chapter-number]').html(lesson.chapterNumber).parent().show().next().show();
                } else {
                    that.element.find('[data-role=chapter-number]').parent().hide().next().hide();
                }

                if (parseInt(lesson.unitNumber) > 0) {
                    that.element.find('[data-role=unit-number]').html(lesson.unitNumber).parent().show().next().show();
                } else {
                    that.element.find('[data-role=unit-number]').parent().hide().next().hide();
                }

                if ( (lesson.status != 'published') && !/preview=1/.test(window.location.href)) {
                    $("#lesson-unpublished-content").show();
                    return;
                }

                var number = lesson.number -1;

                if (lesson.canLearn.status != 'yes') {
                    $("#lesson-alert-content .lesson-content-text-body").html(lesson.canLearn.message);
                    $("#lesson-alert-content").show();
                    return;
                }

                if (lesson.mediaError) {
                    Notify.danger(lesson.mediaError);
                    return ;
                }

                if (lesson.mediaSource == 'iframe') {
                    var html = '<iframe src="' + lesson.mediaUri + '" style="position:absolute; left:0; top:0; height:100%; width:100%; border:0px;" scrolling="no"></iframe>';

                    $("#lesson-iframe-content").html(html);
                    $("#lesson-iframe-content").show();

                } else if (lesson.type == 'video' || lesson.type == 'audio') {
                    if(lesson.mediaSource == 'self') {
                        var lessonVideoDiv = $('#lesson-video-content');

                        if ((lesson.mediaConvertStatus == 'waiting') || (lesson.mediaConvertStatus == 'doing')) {
                            Notify.warning('视频文件正在转换中，稍后完成后即可查看');
                            return ;
                        }

                        var playerUrl = '../../course/' + lesson.courseId + '/lesson/' + lesson.id + '/player';
                        var html = '<iframe src=\''+playerUrl+'\' name=\'viewerIframe\' id=\'viewerIframe\' width=\'100%\'allowfullscreen webkitallowfullscreen height=\'100%\' style=\'border:0px\'></iframe>';

                        $("#lesson-video-content").show();
                        $("#lesson-video-content").html(html);

                        var messenger = new Messenger({
                            name: 'parent',
                            project: 'PlayerProject',
                            children: [ document.getElementById('viewerIframe') ],
                            type: 'parent'
                        });

                        messenger.on("ended", function(){
                            var player = that.get("player");
                            player.playing = false;
                            that.set("player", player);
                            that._onFinishLearnLesson();
                        });

                        messenger.on("playing", function(){
                            var player = that.get("player");
                            player.playing = true;
                            that.set("player", player);
                        });

                        messenger.on("paused", function(){
                            var player = that.get("player");
                            player.playing = false;
                            that.set("player", player);
                        });

                        that.set("player", {});

                    } else {
                        $("#lesson-swf-content").html('<div id="lesson-swf-player"></div>');
                        swfobject.embedSWF(lesson.mediaUri, 
                            'lesson-swf-player', '100%', '100%', "9.0.0", null, null, 
                            {wmode:'opaque',allowFullScreen:'true'});
                        $("#lesson-swf-content").show();
                    }
                } else if (lesson.type == 'text' ) {
                    $("#lesson-text-content").find('.lesson-content-text-body').html(lesson.content);
                    $("#lesson-text-content").show();
                    $("#lesson-text-content").perfectScrollbar({wheelSpeed:50});
                    $("#lesson-text-content").scrollTop(0);
                    $("#lesson-text-content").perfectScrollbar('update');

                } else if (lesson.type =="live") {
                    var liveStartTimeFormat = lesson.startTimeFormat;
                    var liveEndTimeFormat = lesson.endTimeFormat;
                    var startTime = lesson.startTime;
                    var endTime = lesson.endTime;

                    var courseId = lesson.courseId;
                    var lessonId = lesson.id;
                    var $liveNotice = "<p>直播将于 <strong>"+liveStartTimeFormat+"</strong> 开始，于 <strong>"+liveEndTimeFormat+"</strong> 结束，请在课前10分钟内提早进入。</p>";
                    if(iID) {
                        clearInterval(iID);
                    }

                    var intervalSecond = 0;

                    function generateHtml() {
                        var nowDate = lesson.nowDate + intervalSecond;
                        var startLeftSeconds = parseInt(startTime - nowDate);
                        var endLeftSeconds = parseInt(endTime - nowDate);
                        var days = Math.floor(startLeftSeconds / (60 * 60 * 24));
                        var modulo = startLeftSeconds % (60 * 60 * 24);
                        var hours = Math.floor(modulo / (60 * 60));
                        modulo = modulo % (60 * 60);
                        var minutes = Math.floor(modulo / 60);
                        var seconds = modulo % 60;
                        var $replayGuid = "老师们：";
                        $replayGuid += "<br>";

                        if(lesson.liveProvider == 1) {
                            $replayGuid += "&nbsp;&nbsp;&nbsp;&nbsp;录制直播课程时，需在直播课程间点击“";
                            $replayGuid += "<span style='color:red'>录制面板</span>";
                            $replayGuid += "”，录制完成后点击“";
                            $replayGuid += "<span style='color:red'>暂停</span>”";
                            $replayGuid += "结束录播，录播结束后在“";
                            $replayGuid += "<span style='color:red'>录播管理</span>";
                            $replayGuid += "”界面生成回放。";
                            $replayGuid += "<br>";
                        } else {
                            $replayGuid += "&nbsp;&nbsp;&nbsp;&nbsp;";
                            $replayGuid += "直播平台“<span style='color:red'>下课后</span>”且“<span style='color:red'>直播时间</span>”结束后，在课时管理的“";
                            $replayGuid += "<span style='color:red'>录播管理</span>";
                            $replayGuid += "”点击生成回放。";
                            $replayGuid += "<br>";
                        }


                        $countDown = "还剩: <strong class='text-info'>" + days + "</strong>天<strong class='text-info'>" + hours + "</strong>小时<strong class='text-info'>" + minutes + "</strong>分钟<strong>" + seconds + "</strong>秒<br><br>";

                        if (days == 0) {
                            $countDown = "还剩: <strong class='text-info'>" + hours + "</strong>小时<strong class='text-info'>" + minutes + "</strong>分钟<strong class='text-info'>" + seconds + "</strong>秒<br><br>";
                        };

                        if (hours == 0 && days != 0) {
                            $countDown = "还剩: <strong class='text-info'>" + days + "</strong>天<strong class='text-info'>" + minutes + "</strong>分钟<strong class='text-info'>" + seconds + "</strong>秒<br><br>";
                        };

                        if (hours == 0 && days == 0) {
                            $countDown = "还剩: <strong class='text-info'>" + minutes + "</strong>分钟<strong class='text-info'>" + seconds + "</strong>秒<br><br>";
                        };

                        if (0< startLeftSeconds && startLeftSeconds < 7200) {
                             $liveNotice = "<p>直播将于 <strong>"+liveStartTimeFormat+"</strong> 开始，于 <strong>"+liveEndTimeFormat+"</strong> 结束，请在课前10分钟内提早进入。</p>";
                             var url = self.get('courseUri') + '/lesson/' + id + '/live_entry';
                             if(lesson.isTeacher) {
                                $countDown = $replayGuid;
                                $countDown += "<p>还剩"+ hours + "小时"+ minutes+ "分钟"+seconds + "秒&nbsp;<a class='btn btn-primary' href='" + url + "' target='_blank'>进入直播教室</a><br><br></p>";
                            }else{
                                $countDown = "<p>还剩"+ hours + "小时"+ minutes+ "分钟"+seconds + "秒&nbsp;<a class='btn btn-primary' href='" + url + "' target='_blank'>进入直播教室</a><br><br></p>";
                            }
                        };

                        if (startLeftSeconds <= 0) {
                            clearInterval(iID);
                             $liveNotice = "<p>直播已经开始，直播将于 <strong>"+liveEndTimeFormat+"</strong> 结束。</p>";
                             var url = self.get('courseUri') + '/lesson/' + id + '/live_entry';
                            if(lesson.isTeacher) {
                                $countDown = $replayGuid;
                                $countDown += "<p><a class='btn btn-primary' href='" + url + "' target='_blank'>进入直播教室</a><br><br></p>";
                            }else{
                                $countDown = "<p><a class='btn btn-primary' href='" + url + "' target='_blank'>进入直播教室</a><br><br></p>";
                            }
                        };

                        if (endLeftSeconds <= 0) {
                            $liveNotice = "<p>直播已经结束</p>";
                            $countDown = "";
                            if(lesson.replays && lesson.replays.length>0){
                                $.each(lesson.replays, function(i,n){
                                    $countDown += "<a class='btn btn-primary' href='"+n.url+"' target='_blank'>"+n.title+"</a>&nbsp;&nbsp;";
                                });
                            }
                        };

                        $("#lesson-live-content").find('.lesson-content-text-body').html($liveNotice + '<div style="padding-bottom:15px; border-bottom:1px dashed #ccc;">' + lesson.summary + '</div>' + '<br>' + $countDown);

                        intervalSecond++;
                    }

                    generateHtml();

                    iID = setInterval(generateHtml, 1000);

                    $("#lesson-live-content").show();
                    $("#lesson-live-content").perfectScrollbar({wheelSpeed:50});
                    $("#lesson-live-content").scrollTop(0);
                    $("#lesson-live-content").perfectScrollbar('update');

                } else if (lesson.type == 'testpaper') {
                    var url = '../../test/' + lesson.mediaId + '/do?targetType=lesson&targetId=' + id;
                    var html = '<span class="text-info">请点击「开始考试」按钮，在新开窗口中完成考试。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">开始考试</a></span>';
                    var html = '<span class="text-info">正在载入，请稍等...</span>';
                    $("#lesson-testpaper-content").find('.lesson-content-text-body').html(html);
                    $("#lesson-testpaper-content").show();

                    $.get('../../testpaper/' + lesson.mediaId + '/user_result/json', function(result) {
                        if (result.error) {
                            html = '<span class="text-danger">' + result.error + '</span>';
                        } else {
                            if (result.status == 'nodo') {
                                html = '欢迎参加考试，请点击「开始考试」按钮。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">开始考试</a>';
                            } else if (result.status == 'finished') {
                                var redoUrl = '../../test/' + lesson.mediaId + '/redo?targetType=lesson&targetId=' + id;
                                var resultUrl = '../../test/' + result.resultId + '/result?targetType=lesson&targetId=' + id;
                                html = '试卷已批阅。' + '<a href="' + redoUrl + '" class="btn btn-default btn-sm" target="_blank">再做一次</a>' + '<a href="' + resultUrl + '" class="btn btn-link btn-sm" target="_blank">查看结果</a>';
                            } else if (result.status == 'doing' || result.status == 'paused') {
                                html = '试卷未完全做完。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">继续考试</a>';
                            } else if (result.status == 'reviewing') {
                                html = '试卷正在批阅。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">查看试卷</a>'
                            }
                        }

                        $("#lesson-testpaper-content").find('.lesson-content-text-body').html(html);

                    }, 'json');

                } else if (lesson.type == 'ppt') {
                    $.get(that.get('courseUri') + '/lesson/' + id + '/ppt', function(response) {
                        if (response.error) {
                            var html = '<div class="lesson-content-text-body text-danger">' + response.error.message + '</div>';
                            $("#lesson-ppt-content").html(html).show();
                            return ;
                        }

                        var html = '<div class="slide-player"><div class="slide-player-body loading-background"></div><div class="slide-notice"><div class="header">已经到最后一张图片了哦<button type="button" class="close">×</button></div></div><div class="slide-player-control clearfix"><a href="javascript:" class="goto-first"><span class="glyphicon glyphicon-step-backward"></span></a><a href="javascript:" class="goto-prev"><span class="glyphicon glyphicon-chevron-left"></span></a><a href="javascript:" class="goto-next"><span class="glyphicon glyphicon-chevron-right"></span></span></a><a href="javascript:" class="goto-last"><span class="glyphicon glyphicon-step-forward"></span></a><a href="javascript:" class="fullscreen"><span class="glyphicon glyphicon-fullscreen"></span></a><div class="goto-index-input"><input type="text" class="goto-index form-control input-sm" value="1">&nbsp;/&nbsp;<span class="total"></span></div></div></div>';
                        $("#lesson-ppt-content").html(html).show();

                        var watermarkUrl = $("#lesson-ppt-content").data('watermarkUrl');
                        if (watermarkUrl) {
                            $.get(watermarkUrl, function(watermark) {
                                var player = new SlidePlayer({
                                    element: '.slide-player',
                                    slides: response,
                                    watermark: watermark
                                });
                            });

                        } else {
                            var player = new SlidePlayer({
                                element: '.slide-player',
                                slides: response
                            });
                        }


                    }, 'json');
                } else if (lesson.type == 'document' ) {

                    $.get(that.get('courseUri') + '/lesson/' + id + '/document', function(response) {
                        if (response.error) {
                            var html = '<div class="lesson-content-text-body text-danger">' + response.error.message + '</div>';
                            $("#lesson-document-content").html(html).show();
                            return ;
                        }

                        var html = '<iframe id=\'viewerIframe\' width=\'100%\'allowfullscreen webkitallowfullscreen height=\'100%\'></iframe>';
                        $("#lesson-document-content").html(html).show();

                        var watermarkUrl = $("#lesson-document-content").data('watermarkUrl');
                        if (watermarkUrl) {
                            $.get(watermarkUrl, function(watermark) {
                                var player = new DocumentPlayer({
                                    element: '#lesson-document-content',
                                    swfFileUrl:response.swfUri,
                                    pdfFileUrl:response.pdfUri,
                                    watermark: {
                                        'xPosition': 'center',
                                        'yPosition': 'center',
                                        'rotate': 45,
                                        'contents': watermark
                                    }
                                });
                            });
                        } else {
                            var player = new DocumentPlayer({
                                element: '#lesson-document-content',
                                swfFileUrl:response.swfUri,
                                pdfFileUrl:response.pdfUri
                            });
                        }
                    }, 'json');
                } else if (lesson.type == 'flash' ) {
                    
                    if (!swfobject.hasFlashPlayerVersion('11')) {
                        var html = '<div class="alert alert-warning alert-dismissible fade in" role="alert">';
                        html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                        html += '<span aria-hidden="true">×</span>';
                        html += '</button>';
                        html += '您的浏览器未装Flash播放器或版本太低，请先安装Flash播放器。';
                        html += '</div>';
                        $("#lesson-swf-content").html(html);
                        $("#lesson-swf-content").show();
                    } else {
                        $("#lesson-swf-content").html('<div id="lesson-swf-player"></div>');
                        swfobject.embedSWF(lesson.mediaUri, 
                            'lesson-swf-player', '100%', '100%', "9.0.0", null, null, 
                            {wmode:'opaque',allowFullScreen:'true'});
                        $("#lesson-swf-content").show();
                    }

                }


                if (lesson.type == 'testpaper') {
                    that.element.find('[data-role=finish-lesson]').hide();
                } else {
                    if (!that.element.data('hideMediaLessonLearnBtn')) {
                        that.element.find('[data-role=finish-lesson]').show();
                    } else {
                        if (lesson.type == 'video' || lesson.type == 'audio') {
                            that.element.find('[data-role=finish-lesson]').hide();
                        } else {
                            that.element.find('[data-role=finish-lesson]').show();
                        }
                    }
                }

                that._toolbar.set('lesson', lesson);
                that._startLesson();
                that._afterLoadLesson(id);
            }, 'json');

            $.get(this.get('courseUri') + '/lesson/' + id + '/learn/status', function(json) {
                var $finishButton = that.element.find('[data-role=finish-lesson]');
                if (json.status != 'finished') {
                    $finishButton.removeClass('btn-success');
                    $finishButton.find('.glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
                } else {
                    $finishButton.addClass('btn-success');
                    $finishButton.find('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
                }
            }, 'json');

            this._showOrHideNavBtn();

        },

        _showOrHideNavBtn: function() {
            var $prevBtn = this.$('[data-role=prev-lesson]'),
                $nextBtn = this.$('[data-role=next-lesson]'),
                index = $.inArray(parseInt(this.get('lessonId')), this._lessons);
            $prevBtn.show();
            $nextBtn.show();

            if (index < 0) {
                return ;
            }

            if (index === 0) {
                $prevBtn.hide();
            } else if (index === (this._lessons.length - 1)) {
                $nextBtn.hide();
            }

        },

        _getNextLessonId: function(e) {

            var index = $.inArray(parseInt(this.get('lessonId')), this._lessons);
            if (index < 0) {
                return -1;
            }

            if (index + 1 >= this._lessons.length) {
                return -1;
            }

            return this._lessons[index+1];
        },

        _getPrevLessonId: function(e) {
            var index = $.inArray(parseInt(this.get('lessonId')), this._lessons);
            if (index < 0) {
                return -1;
            }

            if (index == 0 ) {
                return -1;
            }

            return this._lessons[index-1];
        },
        _initChapter: function(e) {
           this.chapterAnimate = new chapterAnimate({
            'element': this.element
           });
        }

    });

    var Counter = Class.create({
        initialize: function(dashboard, courseId, lessonId, watchLimit) {
            this.dashboard = dashboard;
            this.courseId = courseId;
            this.lessonId = lessonId;
            this.interval = 120;
            this.watched = false;
            this.watchLimit = watchLimit;
        },

        setTimerId: function(timerId) {
            this.timerId = timerId;
        },

        execute: function(){
            var posted = this.addMediaPlayingCounter();
            this.addLearningCounter(posted);
        },

        addLearningCounter: function(promptlyPost) {
            var learningCounter = Store.get("lesson_id_"+this.lessonId+"_learning_counter");
            if(!learningCounter){
                learningCounter = 0;
            }
            learningCounter++;

            if(promptlyPost || learningCounter >= this.interval){
                var url="../../../../course/"+this.lessonId+'/learn/time/'+learningCounter;
                $.get(url);
                learningCounter = 0;
            }

            Store.set("lesson_id_"+this.lessonId+"_learning_counter", learningCounter);
        },

        addMediaPlayingCounter: function() {
            var mediaPlayingCounter = Store.get("lesson_id_"+this.lessonId+"_playing_counter");
            if(!mediaPlayingCounter){
                mediaPlayingCounter = 0;
            }
            if(this.dashboard == undefined || this.dashboard.get("player") == undefined) {
                return;
            }

            var playing = this.dashboard.get("player").playing;
            var posted = false;
            if(mediaPlayingCounter >= this.interval || (mediaPlayingCounter>0 && !playing)){
                var url="../../../../course/"+this.lessonId+'/watch/time/'+mediaPlayingCounter;
                var self = this;
                $.get(url, function(response) {
                    if (self.watchLimit && response.watchLimited) {
                        window.location.reload();
                    }
                }, 'json');
                posted = true;
                mediaPlayingCounter = 0;
            } else if(playing) {
                mediaPlayingCounter++;
            }

            Store.set("lesson_id_"+this.lessonId+"_playing_counter", mediaPlayingCounter);

            return posted;
        }
    });

    exports.run = function() {
        
        var dashboard = new LessonDashboard({
            element: '#lesson-dashboard'
        }).render();
        $(".es-qrcode").click(function(){
            var $this = $(this); 
            var url=document.location.href.split("#");
            var id=url[1].split("/");
            if($this.hasClass('open')) {
                $this.removeClass('open');
            }else {
                $.ajax({
                    type: "post",
                    url: $this.data("url")+"&lessonId="+id[1],
                    dataType: "json",
                    success:function(data){
                        $this.find(".qrcode-popover img").attr("src",data.img);
                        $this.addClass('open');
                    }
                });
            }
        });
    };

});
