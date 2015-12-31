$(document).ready(function() {
    
    bodyFixed();
    
    ;(function($){var h=$.scrollTo=function(a,b,c){$(window).scrollTo(a,b,c)};h.defaults={axis:'xy',duration:parseFloat($.fn.jquery)>=1.3?0:1,limit:true};h.window=function(a){return $(window)._scrollable()};$.fn._scrollable=function(){return this.map(function(){var a=this,isWin=!a.nodeName||$.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!isWin)return a;var b=(a.contentWindow||a).document||a.ownerDocument||a;return/webkit/i.test(navigator.userAgent)||b.compatMode=='BackCompat'?b.body:b.documentElement})};$.fn.scrollTo=function(e,f,g){if(typeof f=='object'){g=f;f=0}if(typeof g=='function')g={onAfter:g};if(e=='max')e=9e9;g=$.extend({},h.defaults,g);f=f||g.duration;g.queue=g.queue&&g.axis.length>1;if(g.queue)f/=2;g.offset=both(g.offset);g.over=both(g.over);return this._scrollable().each(function(){if(e==null)return;var d=this,$elem=$(d),targ=e,toff,attr={},win=$elem.is('html,body');switch(typeof targ){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(targ)){targ=both(targ);break}targ=$(targ,this);if(!targ.length)return;case'object':if(targ.is||targ.style)toff=(targ=$(targ)).offset()}$.each(g.axis.split(''),function(i,a){var b=a=='x'?'Left':'Top',pos=b.toLowerCase(),key='scroll'+b,old=d[key],max=h.max(d,a);if(toff){attr[key]=toff[pos]+(win?0:old-$elem.offset()[pos]);if(g.margin){attr[key]-=parseInt(targ.css('margin'+b))||0;attr[key]-=parseInt(targ.css('border'+b+'Width'))||0}attr[key]+=g.offset[pos]||0;if(g.over[pos])attr[key]+=targ[a=='x'?'width':'height']()*g.over[pos]}else{var c=targ[pos];attr[key]=c.slice&&c.slice(-1)=='%'?parseFloat(c)/100*max:c}if(g.limit&&/^\d+$/.test(attr[key]))attr[key]=attr[key]<=0?0:Math.min(attr[key],max);if(!i&&g.queue){if(old!=attr[key])animate(g.onAfterFirst);delete attr[key]}});animate(g.onAfter);function animate(a){$elem.animate(attr,f,g.easing,a&&function(){a.call(this,e,g)})}}).end()};h.max=function(a,b){var c=b=='x'?'Width':'Height',scroll='scroll'+c;if(!$(a).is('html,body'))return a[scroll]-$(a)[c.toLowerCase()]();var d='client'+c,html=a.ownerDocument.documentElement,body=a.ownerDocument.body;return Math.max(html[scroll],body[scroll])-Math.min(html[d],body[d])};function both(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);

    ;(function ($, w, undefined) {
        'use strict';

        var pluginName = 'sly';
        var className  = 'Sly';
        var namespace  = pluginName;

        // Local WindowAnimationTiming interface
        var cAF = w.cancelAnimationFrame || w.cancelRequestAnimationFrame;
        var rAF = w.requestAnimationFrame;

        // Support indicators
        var transform, gpuAcceleration;

        // Other global values
        var $doc = $(document);
        var dragInitEvents = 'touchstart.' + namespace + ' mousedown.' + namespace;
        var dragMouseEvents = 'mousemove.' + namespace + ' mouseup.' + namespace;
        var dragTouchEvents = 'touchmove.' + namespace + ' touchend.' + namespace;
        var wheelEvent = (document.implementation.hasFeature('Event.wheel', '3.0') ? 'wheel.' : 'mousewheel.') + namespace;
        var clickEvent = 'click.' + namespace;
        var mouseDownEvent = 'mousedown.' + namespace;
        var interactiveElements = ['INPUT', 'SELECT', 'BUTTON', 'TEXTAREA'];
        var tmpArray = [];
        var time;

        // Math shorthands
        var abs = Math.abs;
        var sqrt = Math.sqrt;
        var pow = Math.pow;
        var round = Math.round;
        var max = Math.max;
        var min = Math.min;

        // Keep track of last fired global wheel event
        var lastGlobalWheel = 0;
        $doc.on(wheelEvent, function (event) {
            var sly = event.originalEvent[namespace];
            var time = +new Date();
            // Update last global wheel time, but only when event didn't originate
            // in Sly frame, or the origin was less than scrollHijack time ago
            if (!sly || sly.options.scrollHijack < time - lastGlobalWheel) lastGlobalWheel = time;
        });

        /**
         * Sly.
         *
         * @class
         *
         * @param {Element} frame       DOM element of sly container.
         * @param {Object}  options     Object with options.
         * @param {Object}  callbackMap Callbacks map.
         */
        function Sly(frame, options, callbackMap) {
            if (!(this instanceof Sly)) return new Sly(frame, options, callbackMap);

            // Extend options
            var o = $.extend({}, Sly.defaults, options);

            // Private variables
            var self = this;
            var parallax = isNumber(frame);

            // Frame
            var $frame = $(frame);
            var $slidee = o.slidee ? $(o.slidee).eq(0) : $frame.children().eq(0);
            var frameSize = 0;
            var slideeSize = 0;
            var pos = {
                start: 0,
                center: 0,
                end: 0,
                cur: 0,
                dest: 0
            };

            // Scrollbar
            var $sb = $(o.scrollBar).eq(0);
            var $handle = $sb.children().eq(0);
            var sbSize = 0;
            var handleSize = 0;
            var hPos = {
                start: 0,
                end: 0,
                cur: 0
            };

            // Pagesbar
            var $pb = $(o.pagesBar);
            var $pages = 0;
            var pages = [];

            // Items
            var $items = 0;
            var items = [];
            var rel = {
                firstItem: 0,
                lastItem: 0,
                centerItem: 0,
                activeItem: null,
                activePage: 0
            };

            // Styles
            var frameStyles = new StyleRestorer($frame[0]);
            var slideeStyles = new StyleRestorer($slidee[0]);
            var sbStyles = new StyleRestorer($sb[0]);
            var handleStyles = new StyleRestorer($handle[0]);

            // Navigation type booleans
            var basicNav = o.itemNav === 'basic';
            var forceCenteredNav = o.itemNav === 'forceCentered';
            var centeredNav = o.itemNav === 'centered' || forceCenteredNav;
            var itemNav = !parallax && (basicNav || centeredNav || forceCenteredNav);

            // Miscellaneous
            var $scrollSource = o.scrollSource ? $(o.scrollSource) : $frame;
            var $dragSource = o.dragSource ? $(o.dragSource) : $frame;
            var $forwardButton = $(o.forward);
            var $backwardButton = $(o.backward);
            var $prevButton = $(o.prev);
            var $nextButton = $(o.next);
            var $prevPageButton = $(o.prevPage);
            var $nextPageButton = $(o.nextPage);
            var callbacks = {};
            var last = {};
            var animation = {};
            var move = {};
            var dragging = {
                released: 1
            };
            var scrolling = {
                last: 0,
                delta: 0,
                resetTime: 200
            };
            var renderID = 0;
            var historyID = 0;
            var cycleID = 0;
            var continuousID = 0;
            var i, l;

            // Normalizing frame
            if (!parallax) {
                frame = $frame[0];
            }

            // Expose properties
            self.initialized = 0;
            self.frame = frame;
            self.slidee = $slidee[0];
            self.pos = pos;
            self.rel = rel;
            self.items = items;
            self.pages = pages;
            self.isPaused = 0;
            self.options = o;
            self.dragging = dragging;

            /**
             * Loading function.
             *
             * Populate arrays, set sizes, bind events, ...
             *
             * @param {Boolean} [isInit] Whether load is called from within self.init().
             * @return {Void}
             */
            function load(isInit) {
                // Local variables
                var lastItemsCount = 0;
                var lastPagesCount = pages.length;

                // Save old position
                pos.old = $.extend({}, pos);

                // Reset global variables
                frameSize = parallax ? 0 : $frame[o.horizontal ? 'width' : 'height']();
                sbSize = $sb[o.horizontal ? 'width' : 'height']();
                slideeSize = parallax ? frame : $slidee[o.horizontal ? 'outerWidth' : 'outerHeight']();
                pages.length = 0;

                // Set position limits & relatives
                pos.start = 0;
                pos.end = max(slideeSize - frameSize, 0);

                // Sizes & offsets for item based navigations
                if (itemNav) {
                    // Save the number of current items
                    lastItemsCount = items.length;

                    // Reset itemNav related variables
                    $items = $slidee.children(o.itemSelector);
                    items.length = 0;

                    // Needed variables
                    var paddingStart = getPx($slidee, o.horizontal ? 'paddingLeft' : 'paddingTop');
                    var paddingEnd = getPx($slidee, o.horizontal ? 'paddingRight' : 'paddingBottom');
                    var borderBox = $($items).css('boxSizing') === 'border-box';
                    var areFloated = $items.css('float') !== 'none';
                    var ignoredMargin = 0;
                    var lastItemIndex = $items.length - 1;
                    var lastItem;

                    // Reset slideeSize
                    slideeSize = 0;

                    // Iterate through items
                    $items.each(function (i, element) {
                        // Item
                        var $item = $(element);
                        var rect = element.getBoundingClientRect();
                        var itemSize = round(o.horizontal ? rect.width || rect.right - rect.left : rect.height || rect.bottom - rect.top);
                        var itemMarginStart = getPx($item, o.horizontal ? 'marginLeft' : 'marginTop');
                        var itemMarginEnd = getPx($item, o.horizontal ? 'marginRight' : 'marginBottom');
                        var itemSizeFull = itemSize + itemMarginStart + itemMarginEnd;
                        var singleSpaced = !itemMarginStart || !itemMarginEnd;
                        var item = {};
                        item.el = element;
                        item.size = singleSpaced ? itemSize : itemSizeFull;
                        item.half = item.size / 2;
                        item.start = slideeSize + (singleSpaced ? itemMarginStart : 0);
                        item.center = item.start - round(frameSize / 2 - item.size / 2);
                        item.end = item.start - frameSize + item.size;

                        // Account for slidee padding
                        if (!i) {
                            slideeSize += paddingStart;
                        }

                        // Increment slidee size for size of the active element
                        slideeSize += itemSizeFull;

                        // Try to account for vertical margin collapsing in vertical mode
                        // It's not bulletproof, but should work in 99% of cases
                        if (!o.horizontal && !areFloated) {
                            // Subtract smaller margin, but only when top margin is not 0, and this is not the first element
                            if (itemMarginEnd && itemMarginStart && i > 0) {
                                slideeSize -= min(itemMarginStart, itemMarginEnd);
                            }
                        }

                        // Things to be done on last item
                        if (i === lastItemIndex) {
                            item.end += paddingEnd;
                            slideeSize += paddingEnd;
                            ignoredMargin = singleSpaced ? itemMarginEnd : 0;
                        }

                        // Add item object to items array
                        items.push(item);
                        lastItem = item;
                    });

                    // Resize SLIDEE to fit all items
                    $slidee[0].style[o.horizontal ? 'width' : 'height'] = (borderBox ? slideeSize: slideeSize - paddingStart - paddingEnd) + 'px';

                    // Adjust internal SLIDEE size for last margin
                    slideeSize -= ignoredMargin;

                    // Set limits
                    if (items.length) {
                        pos.start =  items[0][forceCenteredNav ? 'center' : 'start'];
                        pos.end = forceCenteredNav ? lastItem.center : frameSize < slideeSize ? lastItem.end : pos.start;
                    } else {
                        pos.start = pos.end = 0;
                    }
                }

                // Calculate SLIDEE center position
                pos.center = round(pos.end / 2 + pos.start / 2);

                // Update relative positions
                updateRelatives();

                // Scrollbar
                if ($handle.length && sbSize > 0) {
                    // Stretch scrollbar handle to represent the visible area
                    if (o.dynamicHandle) {
                        handleSize = pos.start === pos.end ? sbSize : round(sbSize * frameSize / slideeSize);
                        handleSize = within(handleSize, o.minHandleSize, sbSize);
                        $handle[0].style[o.horizontal ? 'width' : 'height'] = handleSize + 'px';
                    } else {
                        handleSize = $handle[o.horizontal ? 'outerWidth' : 'outerHeight']();
                    }

                    hPos.end = sbSize - handleSize;

                    if (!renderID) {
                        syncScrollbar();
                    }
                }

                // Pages
                if (!parallax && frameSize > 0) {
                    var tempPagePos = pos.start;
                    var pagesHtml = '';

                    // Populate pages array
                    if (itemNav) {
                        $.each(items, function (i, item) {
                            if (forceCenteredNav) {
                                pages.push(item.center);
                            } else if (item.start + item.size > tempPagePos && tempPagePos <= pos.end) {
                                tempPagePos = item.start;
                                pages.push(tempPagePos);
                                tempPagePos += frameSize;
                                if (tempPagePos > pos.end && tempPagePos < pos.end + frameSize) {
                                    pages.push(pos.end);
                                }
                            }
                        });
                    } else {
                        while (tempPagePos - frameSize < pos.end) {
                            pages.push(tempPagePos);
                            tempPagePos += frameSize;
                        }
                    }

                    // Pages bar
                    if ($pb[0] && lastPagesCount !== pages.length) {
                        for (var i = 0; i < pages.length; i++) {
                            pagesHtml += o.pageBuilder.call(self, i);
                        }
                        $pages = $pb.html(pagesHtml).children();
                        $pages.eq(rel.activePage).addClass(o.activeClass);
                    }
                }

                // Extend relative variables object with some useful info
                rel.slideeSize = slideeSize;
                rel.frameSize = frameSize;
                rel.sbSize = sbSize;
                rel.handleSize = handleSize;

                // Activate requested position
                if (itemNav) {
                    if (isInit && o.startAt != null) {
                        activate(o.startAt);
                        self[centeredNav ? 'toCenter' : 'toStart'](o.startAt);
                    }
                    // Fix possible overflowing
                    var activeItem = items[rel.activeItem];
                    slideTo(centeredNav && activeItem ? activeItem.center : within(pos.dest, pos.start, pos.end));
                } else {
                    if (isInit) {
                        if (o.startAt != null) slideTo(o.startAt, 1);
                    } else {
                        // Fix possible overflowing
                        slideTo(within(pos.dest, pos.start, pos.end));
                    }
                }

                // Trigger load event
                trigger('load');
            }
            self.reload = function () { load(); };

            /**
             * Animate to a position.
             *
             * @param {Int}  newPos    New position.
             * @param {Bool} immediate Reposition immediately without an animation.
             * @param {Bool} dontAlign Do not align items, use the raw position passed in first argument.
             *
             * @return {Void}
             */
            function slideTo(newPos, immediate, dontAlign) {
                // Align items
                if (itemNav && dragging.released && !dontAlign) {
                    var tempRel = getRelatives(newPos);
                    var isNotBordering = newPos > pos.start && newPos < pos.end;

                    if (centeredNav) {
                        if (isNotBordering) {
                            newPos = items[tempRel.centerItem].center;
                        }
                        if (forceCenteredNav && o.activateMiddle) {
                            activate(tempRel.centerItem);
                        }
                    } else if (isNotBordering) {
                        newPos = items[tempRel.firstItem].start;
                    }
                }

                // Handle overflowing position limits
                if (dragging.init && dragging.slidee && o.elasticBounds) {
                    if (newPos > pos.end) {
                        newPos = pos.end + (newPos - pos.end) / 6;
                    } else if (newPos < pos.start) {
                        newPos = pos.start + (newPos - pos.start) / 6;
                    }
                } else {
                    newPos = within(newPos, pos.start, pos.end);
                }

                // Update the animation object
                animation.start = +new Date();
                animation.time = 0;
                animation.from = pos.cur;
                animation.to = newPos;
                animation.delta = newPos - pos.cur;
                animation.tweesing = dragging.tweese || dragging.init && !dragging.slidee;
                animation.immediate = !animation.tweesing && (immediate || dragging.init && dragging.slidee || !o.speed);

                // Reset dragging tweesing request
                dragging.tweese = 0;

                // Start animation rendering
                if (newPos !== pos.dest) {
                    pos.dest = newPos;
                    trigger('change');
                    if (!renderID) {
                        render();
                    }
                }

                // Reset next cycle timeout
                resetCycle();

                // Synchronize states
                updateRelatives();
                updateButtonsState();
                syncPagesbar();
            }

            /**
             * Render animation frame.
             *
             * @return {Void}
             */
            function render() {
                if (!self.initialized) {
                    return;
                }

                // If first render call, wait for next animationFrame
                if (!renderID) {
                    renderID = rAF(render);
                    if (dragging.released) {
                        trigger('moveStart');
                    }
                    return;
                }

                // If immediate repositioning is requested, don't animate.
                if (animation.immediate) {
                    pos.cur = animation.to;
                }
                // Use tweesing for animations without known end point
                else if (animation.tweesing) {
                    animation.tweeseDelta = animation.to - pos.cur;
                    // Fuck Zeno's paradox
                    if (abs(animation.tweeseDelta) < 0.1) {
                        pos.cur = animation.to;
                    } else {
                        pos.cur += animation.tweeseDelta * (dragging.released ? o.swingSpeed : o.syncSpeed);
                    }
                }
                // Use tweening for basic animations with known end point
                else {
                    animation.time = min(+new Date() - animation.start, o.speed);
                    pos.cur = animation.from + animation.delta * $.easing[o.easing](animation.time/o.speed, animation.time, 0, 1, o.speed);
                }

                // If there is nothing more to render break the rendering loop, otherwise request new animation frame.
                if (animation.to === pos.cur) {
                    pos.cur = animation.to;
                    dragging.tweese = renderID = 0;
                } else {
                    renderID = rAF(render);
                }

                trigger('move');

                // Update SLIDEE position
                if (!parallax) {
                    if (transform) {
                        $slidee[0].style[transform] = gpuAcceleration + (o.horizontal ? 'translateX' : 'translateY') + '(' + (-pos.cur) + 'px)';
                    } else {
                        $slidee[0].style[o.horizontal ? 'left' : 'top'] = -round(pos.cur) + 'px';
                    }
                }

                // When animation reached the end, and dragging is not active, trigger moveEnd
                if (!renderID && dragging.released) {
                    trigger('moveEnd');
                }

                syncScrollbar();
            }

            /**
             * Synchronizes scrollbar with the SLIDEE.
             *
             * @return {Void}
             */
            function syncScrollbar() {
                if ($handle.length) {
                    hPos.cur = pos.start === pos.end ? 0 : (((dragging.init && !dragging.slidee) ? pos.dest : pos.cur) - pos.start) / (pos.end - pos.start) * hPos.end;
                    hPos.cur = within(round(hPos.cur), hPos.start, hPos.end);
                    if (last.hPos !== hPos.cur) {
                        last.hPos = hPos.cur;
                        if (transform) {
                            $handle[0].style[transform] = gpuAcceleration + (o.horizontal ? 'translateX' : 'translateY') + '(' + hPos.cur + 'px)';
                        } else {
                            $handle[0].style[o.horizontal ? 'left' : 'top'] = hPos.cur + 'px';
                        }
                    }
                }
            }

            /**
             * Synchronizes pagesbar with SLIDEE.
             *
             * @return {Void}
             */
            function syncPagesbar() {
                if ($pages[0] && last.page !== rel.activePage) {
                    last.page = rel.activePage;
                    $pages.removeClass(o.activeClass).eq(rel.activePage).addClass(o.activeClass);
                    trigger('activePage', last.page);
                }
            }

            /**
             * Returns the position object.
             *
             * @param {Mixed} item
             *
             * @return {Object}
             */
            self.getPos = function (item) {
                if (itemNav) {
                    var index = getIndex(item);
                    return index !== -1 ? items[index] : false;
                } else {
                    var $item = $slidee.find(item).eq(0);

                    if ($item[0]) {
                        var offset = o.horizontal ? $item.offset().left - $slidee.offset().left : $item.offset().top - $slidee.offset().top;
                        var size = $item[o.horizontal ? 'outerWidth' : 'outerHeight']();

                        return {
                            start: offset,
                            center: offset - frameSize / 2 + size / 2,
                            end: offset - frameSize + size,
                            size: size
                        };
                    } else {
                        return false;
                    }
                }
            };

            /**
             * Continuous move in a specified direction.
             *
             * @param  {Bool} forward True for forward movement, otherwise it'll go backwards.
             * @param  {Int}  speed   Movement speed in pixels per frame. Overrides options.moveBy value.
             *
             * @return {Void}
             */
            self.moveBy = function (speed) {
                move.speed = speed;
                // If already initiated, or there is nowhere to move, abort
                if (dragging.init || !move.speed || pos.cur === (move.speed > 0 ? pos.end : pos.start)) {
                    return;
                }
                // Initiate move object
                move.lastTime = +new Date();
                move.startPos = pos.cur;
                // Set dragging as initiated
                continuousInit('button');
                dragging.init = 1;
                // Start movement
                trigger('moveStart');
                cAF(continuousID);
                moveLoop();
            };

            /**
             * Continuous movement loop.
             *
             * @return {Void}
             */
            function moveLoop() {
                // If there is nowhere to move anymore, stop
                if (!move.speed || pos.cur === (move.speed > 0 ? pos.end : pos.start)) {
                    self.stop();
                }
                // Request new move loop if it hasn't been stopped
                continuousID = dragging.init ? rAF(moveLoop) : 0;
                // Update move object
                move.now = +new Date();
                move.pos = pos.cur + (move.now - move.lastTime) / 1000 * move.speed;
                // Slide
                slideTo(dragging.init ? move.pos : round(move.pos));
                // Normally, this is triggered in render(), but if there
                // is nothing to render, we have to do it manually here.
                if (!dragging.init && pos.cur === pos.dest) {
                    trigger('moveEnd');
                }
                // Update times for future iteration
                move.lastTime = move.now;
            }

            /**
             * Stops continuous movement.
             *
             * @return {Void}
             */
            self.stop = function () {
                if (dragging.source === 'button') {
                    dragging.init = 0;
                    dragging.released = 1;
                }
            };

            /**
             * Activate previous item.
             *
             * @return {Void}
             */
            self.prev = function () {
                self.activate(rel.activeItem == null ? 0 : rel.activeItem - 1);
            };

            /**
             * Activate next item.
             *
             * @return {Void}
             */
            self.next = function () {
                self.activate(rel.activeItem == null ? 0 : rel.activeItem + 1);
            };

            /**
             * Activate previous page.
             *
             * @return {Void}
             */
            self.prevPage = function () {
                self.activatePage(rel.activePage - 1);
            };

            /**
             * Activate next page.
             *
             * @return {Void}
             */
            self.nextPage = function () {
                self.activatePage(rel.activePage + 1);
            };

            /**
             * Slide SLIDEE by amount of pixels.
             *
             * @param {Int}  delta     Pixels/Items. Positive means forward, negative means backward.
             * @param {Bool} immediate Reposition immediately without an animation.
             *
             * @return {Void}
             */
            self.slideBy = function (delta, immediate) {
                if (!delta) {
                    return;
                }
                if (itemNav) {
                    self[centeredNav ? 'toCenter' : 'toStart'](
                        within((centeredNav ? rel.centerItem : rel.firstItem) + o.scrollBy * delta, 0, items.length)
                    );
                } else {
                    slideTo(pos.dest + delta, immediate);
                }
            };

            /**
             * Animate SLIDEE to a specific position.
             *
             * @param {Int}  pos       New position.
             * @param {Bool} immediate Reposition immediately without an animation.
             *
             * @return {Void}
             */
            self.slideTo = function (pos, immediate) {
                slideTo(pos, immediate);
            };

            /**
             * Core method for handling `toLocation` methods.
             *
             * @param  {String} location
             * @param  {Mixed}  item
             * @param  {Bool}   immediate
             *
             * @return {Void}
             */
            function to(location, item, immediate) {
                // Optional arguments logic
                if (type(item) === 'boolean') {
                    immediate = item;
                    item = undefined;
                }

                if (item === undefined) {
                    slideTo(pos[location], immediate);
                } else {
                    // You can't align items to sides of the frame
                    // when centered navigation type is enabled
                    if (centeredNav && location !== 'center') {
                        return;
                    }

                    var itemPos = self.getPos(item);
                    if (itemPos) {
                        slideTo(itemPos[location], immediate, !centeredNav);
                    }
                }
            }

            /**
             * Animate element or the whole SLIDEE to the start of the frame.
             *
             * @param {Mixed} item      Item DOM element, or index starting at 0. Omitting will animate SLIDEE.
             * @param {Bool}  immediate Reposition immediately without an animation.
             *
             * @return {Void}
             */
            self.toStart = function (item, immediate) {
                to('start', item, immediate);
            };

            /**
             * Animate element or the whole SLIDEE to the end of the frame.
             *
             * @param {Mixed} item      Item DOM element, or index starting at 0. Omitting will animate SLIDEE.
             * @param {Bool}  immediate Reposition immediately without an animation.
             *
             * @return {Void}
             */
            self.toEnd = function (item, immediate) {
                to('end', item, immediate);
            };

            /**
             * Animate element or the whole SLIDEE to the center of the frame.
             *
             * @param {Mixed} item      Item DOM element, or index starting at 0. Omitting will animate SLIDEE.
             * @param {Bool}  immediate Reposition immediately without an animation.
             *
             * @return {Void}
             */
            self.toCenter = function (item, immediate) {
                to('center', item, immediate);
            };

            /**
             * Get the index of an item in SLIDEE.
             *
             * @param {Mixed} item     Item DOM element.
             *
             * @return {Int}  Item index, or -1 if not found.
             */
            function getIndex(item) {
                return item != null ?
                        isNumber(item) ?
                            item >= 0 && item < items.length ? item : -1 :
                            $items.index(item) :
                        -1;
            }
            // Expose getIndex without lowering the compressibility of it,
            // as it is used quite often throughout Sly.
            self.getIndex = getIndex;

            /**
             * Get index of an item in SLIDEE based on a variety of input types.
             *
             * @param  {Mixed} item DOM element, positive or negative integer.
             *
             * @return {Int}   Item index, or -1 if not found.
             */
            function getRelativeIndex(item) {
                return getIndex(isNumber(item) && item < 0 ? item + items.length : item);
            }

            /**
             * Activates an item.
             *
             * @param  {Mixed} item Item DOM element, or index starting at 0.
             *
             * @return {Mixed} Activated item index or false on fail.
             */
            function activate(item, force) {
                var index = getIndex(item);

                if (!itemNav || index < 0) {
                    return false;
                }

                // Update classes, last active index, and trigger active event only when there
                // has been a change. Otherwise just return the current active index.
                if (last.active !== index || force) {
                    // Update classes
                    $items.eq(rel.activeItem).removeClass(o.activeClass);
                    $items.eq(index).addClass(o.activeClass);

                    last.active = rel.activeItem = index;

                    updateButtonsState();
                    trigger('active', index);
                }

                return index;
            }

            /**
             * Activates an item and helps with further navigation when o.smart is enabled.
             *
             * @param {Mixed} item      Item DOM element, or index starting at 0.
             * @param {Bool}  immediate Whether to reposition immediately in smart navigation.
             *
             * @return {Void}
             */
            self.activate = function (item, immediate) {
                var index = activate(item);

                // Smart navigation
                if (o.smart && index !== false) {
                    // When centeredNav is enabled, center the element.
                    // Otherwise, determine where to position the element based on its current position.
                    // If the element is currently on the far end side of the frame, assume that user is
                    // moving forward and animate it to the start of the visible frame, and vice versa.
                    if (centeredNav) {
                        self.toCenter(index, immediate);
                    } else if (index >= rel.lastItem) {
                        self.toStart(index, immediate);
                    } else if (index <= rel.firstItem) {
                        self.toEnd(index, immediate);
                    } else {
                        resetCycle();
                    }
                }
            };

            /**
             * Activates a page.
             *
             * @param {Int}  index     Page index, starting from 0.
             * @param {Bool} immediate Whether to reposition immediately without animation.
             *
             * @return {Void}
             */
            self.activatePage = function (index, immediate) {
                if (isNumber(index)) {
                    slideTo(pages[within(index, 0, pages.length - 1)], immediate);
                }
            };

            /**
             * Return relative positions of items based on their visibility within FRAME.
             *
             * @param {Int} slideePos Position of SLIDEE.
             *
             * @return {Void}
             */
            function getRelatives(slideePos) {
                slideePos = within(isNumber(slideePos) ? slideePos : pos.dest, pos.start, pos.end);

                var relatives = {};
                var centerOffset = forceCenteredNav ? 0 : frameSize / 2;

                // Determine active page
                if (!parallax) {
                    for (var p = 0, pl = pages.length; p < pl; p++) {
                        if (slideePos >= pos.end || p === pages.length - 1) {
                            relatives.activePage = pages.length - 1;
                            break;
                        }

                        if (slideePos <= pages[p] + centerOffset) {
                            relatives.activePage = p;
                            break;
                        }
                    }
                }

                // Relative item indexes
                if (itemNav) {
                    var first = false;
                    var last = false;
                    var center = false;

                    // From start
                    for (var i = 0, il = items.length; i < il; i++) {
                        // First item
                        if (first === false && slideePos <= items[i].start + items[i].half) {
                            first = i;
                        }

                        // Center item
                        if (center === false && slideePos <= items[i].center + items[i].half) {
                            center = i;
                        }

                        // Last item
                        if (i === il - 1 || slideePos <= items[i].end + items[i].half) {
                            last = i;
                            break;
                        }
                    }

                    // Safe assignment, just to be sure the false won't be returned
                    relatives.firstItem = isNumber(first) ? first : 0;
                    relatives.centerItem = isNumber(center) ? center : relatives.firstItem;
                    relatives.lastItem = isNumber(last) ? last : relatives.centerItem;
                }

                return relatives;
            }

            /**
             * Update object with relative positions.
             *
             * @param {Int} newPos
             *
             * @return {Void}
             */
            function updateRelatives(newPos) {
                $.extend(rel, getRelatives(newPos));
            }

            /**
             * Disable navigation buttons when needed.
             *
             * Adds disabledClass, and when the button is <button> or <input>, activates :disabled state.
             *
             * @return {Void}
             */
            function updateButtonsState() {
                var isStart = pos.dest <= pos.start;
                var isEnd = pos.dest >= pos.end;
                var slideePosState = (isStart ? 1 : 0) | (isEnd ? 2 : 0);

                // Update paging buttons only if there has been a change in SLIDEE position
                if (last.slideePosState !== slideePosState) {
                    last.slideePosState = slideePosState;

                    if ($prevPageButton.is('button,input')) {
                        $prevPageButton.prop('disabled', isStart);
                    }

                    if ($nextPageButton.is('button,input')) {
                        $nextPageButton.prop('disabled', isEnd);
                    }

                    $prevPageButton.add($backwardButton)[isStart ? 'addClass' : 'removeClass'](o.disabledClass);
                    $nextPageButton.add($forwardButton)[isEnd ? 'addClass' : 'removeClass'](o.disabledClass);
                }

                // Forward & Backward buttons need a separate state caching because we cannot "property disable"
                // them while they are being used, as disabled buttons stop emitting mouse events.
                if (last.fwdbwdState !== slideePosState && dragging.released) {
                    last.fwdbwdState = slideePosState;

                    if ($backwardButton.is('button,input')) {
                        $backwardButton.prop('disabled', isStart);
                    }

                    if ($forwardButton.is('button,input')) {
                        $forwardButton.prop('disabled', isEnd);
                    }
                }

                // Item navigation
                if (itemNav && rel.activeItem != null) {
                    var isFirst = rel.activeItem === 0;
                    var isLast = rel.activeItem >= items.length - 1;
                    var itemsButtonState = (isFirst ? 1 : 0) | (isLast ? 2 : 0);

                    if (last.itemsButtonState !== itemsButtonState) {
                        last.itemsButtonState = itemsButtonState;

                        if ($prevButton.is('button,input')) {
                            $prevButton.prop('disabled', isFirst);
                        }

                        if ($nextButton.is('button,input')) {
                            $nextButton.prop('disabled', isLast);
                        }

                        $prevButton[isFirst ? 'addClass' : 'removeClass'](o.disabledClass);
                        $nextButton[isLast ? 'addClass' : 'removeClass'](o.disabledClass);
                    }
                }
            }

            /**
             * Resume cycling.
             *
             * @param {Int} priority Resume pause with priority lower or equal than this. Used internally for pauseOnHover.
             *
             * @return {Void}
             */
            self.resume = function (priority) {
                if (!o.cycleBy || !o.cycleInterval || o.cycleBy === 'items' && (!items[0] || rel.activeItem == null) || priority < self.isPaused) {
                    return;
                }

                self.isPaused = 0;

                if (cycleID) {
                    cycleID = clearTimeout(cycleID);
                } else {
                    trigger('resume');
                }

                cycleID = setTimeout(function () {
                    trigger('cycle');
                    switch (o.cycleBy) {
                        case 'items':
                            self.activate(rel.activeItem >= items.length - 1 ? 0 : rel.activeItem + 1);
                            break;

                        case 'pages':
                            self.activatePage(rel.activePage >= pages.length - 1 ? 0 : rel.activePage + 1);
                            break;
                    }
                }, o.cycleInterval);
            };

            /**
             * Pause cycling.
             *
             * @param {Int} priority Pause priority. 100 is default. Used internally for pauseOnHover.
             *
             * @return {Void}
             */
            self.pause = function (priority) {
                if (priority < self.isPaused) {
                    return;
                }

                self.isPaused = priority || 100;

                if (cycleID) {
                    cycleID = clearTimeout(cycleID);
                    trigger('pause');
                }
            };

            /**
             * Toggle cycling.
             *
             * @return {Void}
             */
            self.toggle = function () {
                self[cycleID ? 'pause' : 'resume']();
            };

            /**
             * Updates a signle or multiple option values.
             *
             * @param {Mixed} name  Name of the option that should be updated, or object that will extend the options.
             * @param {Mixed} value New option value.
             *
             * @return {Void}
             */
            self.set = function (name, value) {
                if ($.isPlainObject(name)) {
                    $.extend(o, name);
                } else if (o.hasOwnProperty(name)) {
                    o[name] = value;
                }
            };

            /**
             * Add one or multiple items to the SLIDEE end, or a specified position index.
             *
             * @param {Mixed} element Node element, or HTML string.
             * @param {Int}   index   Index of a new item position. By default item is appended at the end.
             *
             * @return {Void}
             */
            self.add = function (element, index) {
                var $element = $(element);

                if (itemNav) {
                    // Insert the element(s)
                    if (index == null || !items[0] || index >= items.length) {
                        $element.appendTo($slidee);
                    } else if (items.length) {
                        $element.insertBefore(items[index].el);
                    }

                    // Adjust the activeItem index
                    if (rel.activeItem != null && index <= rel.activeItem) {
                        last.active = rel.activeItem += $element.length;
                    }
                } else {
                    $slidee.append($element);
                }

                // Reload
                load();
            };

            /**
             * Remove an item from SLIDEE.
             *
             * @param {Mixed} element Item index, or DOM element.
             * @param {Int}   index   Index of a new item position. By default item is appended at the end.
             *
             * @return {Void}
             */
            self.remove = function (element) {
                if (itemNav) {
                    var index = getRelativeIndex(element);

                    if (index > -1) {
                        // Remove the element
                        $items.eq(index).remove();

                        // If the current item is being removed, activate new one after reload
                        var reactivate = index === rel.activeItem;

                        // Adjust the activeItem index
                        if (rel.activeItem != null && index < rel.activeItem) {
                            last.active = --rel.activeItem;
                        }

                        // Reload
                        load();

                        // Activate new item at the removed position
                        if (reactivate) {
                            last.active = null;
                            self.activate(rel.activeItem);
                        }
                    }
                } else {
                    $(element).remove();
                    load();
                }
            };

            /**
             * Helps re-arranging items.
             *
             * @param  {Mixed} item     Item DOM element, or index starting at 0. Use negative numbers to select items from the end.
             * @param  {Mixed} position Item insertion anchor. Accepts same input types as item argument.
             * @param  {Bool}  after    Insert after instead of before the anchor.
             *
             * @return {Void}
             */
            function moveItem(item, position, after) {
                item = getRelativeIndex(item);
                position = getRelativeIndex(position);

                // Move only if there is an actual change requested
                if (item > -1 && position > -1 && item !== position && (!after || position !== item - 1) && (after || position !== item + 1)) {
                    $items.eq(item)[after ? 'insertAfter' : 'insertBefore'](items[position].el);

                    var shiftStart = item < position ? item : (after ? position : position - 1);
                    var shiftEnd = item > position ? item : (after ? position + 1 : position);
                    var shiftsUp = item > position;

                    // Update activeItem index
                    if (rel.activeItem != null) {
                        if (item === rel.activeItem) {
                            last.active = rel.activeItem = after ? (shiftsUp ? position + 1 : position) : (shiftsUp ? position : position - 1);
                        } else if (rel.activeItem > shiftStart && rel.activeItem < shiftEnd) {
                            last.active = rel.activeItem += shiftsUp ? 1 : -1;
                        }
                    }

                    // Reload
                    load();
                }
            }

            /**
             * Move item after the target anchor.
             *
             * @param  {Mixed} item     Item to be moved. Can be DOM element or item index.
             * @param  {Mixed} position Target position anchor. Can be DOM element or item index.
             *
             * @return {Void}
             */
            self.moveAfter = function (item, position) {
                moveItem(item, position, 1);
            };

            /**
             * Move item before the target anchor.
             *
             * @param  {Mixed} item     Item to be moved. Can be DOM element or item index.
             * @param  {Mixed} position Target position anchor. Can be DOM element or item index.
             *
             * @return {Void}
             */
            self.moveBefore = function (item, position) {
                moveItem(item, position);
            };

            /**
             * Registers callbacks.
             *
             * @param  {Mixed} name  Event name, or callbacks map.
             * @param  {Mixed} fn    Callback, or an array of callback functions.
             *
             * @return {Void}
             */
            self.on = function (name, fn) {
                // Callbacks map
                if (type(name) === 'object') {
                    for (var key in name) {
                        if (name.hasOwnProperty(key)) {
                            self.on(key, name[key]);
                        }
                    }
                // Callback
                } else if (type(fn) === 'function') {
                    var names = name.split(' ');
                    for (var n = 0, nl = names.length; n < nl; n++) {
                        callbacks[names[n]] = callbacks[names[n]] || [];
                        if (callbackIndex(names[n], fn) === -1) {
                            callbacks[names[n]].push(fn);
                        }
                    }
                // Callbacks array
                } else if (type(fn) === 'array') {
                    for (var f = 0, fl = fn.length; f < fl; f++) {
                        self.on(name, fn[f]);
                    }
                }
            };

            /**
             * Registers callbacks to be executed only once.
             *
             * @param  {Mixed} name  Event name, or callbacks map.
             * @param  {Mixed} fn    Callback, or an array of callback functions.
             *
             * @return {Void}
             */
            self.one = function (name, fn) {
                function proxy() {
                    fn.apply(self, arguments);
                    self.off(name, proxy);
                }
                self.on(name, proxy);
            };

            /**
             * Remove one or all callbacks.
             *
             * @param  {String} name Event name.
             * @param  {Mixed}  fn   Callback, or an array of callback functions. Omit to remove all callbacks.
             *
             * @return {Void}
             */
            self.off = function (name, fn) {
                if (fn instanceof Array) {
                    for (var f = 0, fl = fn.length; f < fl; f++) {
                        self.off(name, fn[f]);
                    }
                } else {
                    var names = name.split(' ');
                    for (var n = 0, nl = names.length; n < nl; n++) {
                        callbacks[names[n]] = callbacks[names[n]] || [];
                        if (fn == null) {
                            callbacks[names[n]].length = 0;
                        } else {
                            var index = callbackIndex(names[n], fn);
                            if (index !== -1) {
                                callbacks[names[n]].splice(index, 1);
                            }
                        }
                    }
                }
            };

            /**
             * Returns callback array index.
             *
             * @param  {String}   name Event name.
             * @param  {Function} fn   Function
             *
             * @return {Int} Callback array index, or -1 if isn't registered.
             */
            function callbackIndex(name, fn) {
                for (var i = 0, l = callbacks[name].length; i < l; i++) {
                    if (callbacks[name][i] === fn) {
                        return i;
                    }
                }
                return -1;
            }

            /**
             * Reset next cycle timeout.
             *
             * @return {Void}
             */
            function resetCycle() {
                if (dragging.released && !self.isPaused) {
                    self.resume();
                }
            }

            /**
             * Calculate SLIDEE representation of handle position.
             *
             * @param  {Int} handlePos
             *
             * @return {Int}
             */
            function handleToSlidee(handlePos) {
                return round(within(handlePos, hPos.start, hPos.end) / hPos.end * (pos.end - pos.start)) + pos.start;
            }

            /**
             * Keeps track of a dragging delta history.
             *
             * @return {Void}
             */
            function draggingHistoryTick() {
                // Looking at this, I know what you're thinking :) But as we need only 4 history states, doing it this way
                // as opposed to a proper loop is ~25 bytes smaller (when minified with GCC), a lot faster, and doesn't
                // generate garbage. The loop version would create 2 new variables on every tick. Unexaptable!
                dragging.history[0] = dragging.history[1];
                dragging.history[1] = dragging.history[2];
                dragging.history[2] = dragging.history[3];
                dragging.history[3] = dragging.delta;
            }

            /**
             * Initialize continuous movement.
             *
             * @return {Void}
             */
            function continuousInit(source) {
                dragging.released = 0;
                dragging.source = source;
                dragging.slidee = source === 'slidee';
            }

            /**
             * Dragging initiator.
             *
             * @param  {Event} event
             *
             * @return {Void}
             */
            function dragInit(event) {
                var isTouch = event.type === 'touchstart';
                var source = event.data.source;
                var isSlidee = source === 'slidee';

                // Ignore when already in progress, or interactive element in non-touch navivagion
                if (dragging.init || !isTouch && isInteractive(event.target)) {
                    return;
                }

                // Handle dragging conditions
                if (source === 'handle' && (!o.dragHandle || hPos.start === hPos.end)) {
                    return;
                }

                // SLIDEE dragging conditions
                if (isSlidee && !(isTouch ? o.touchDragging : o.mouseDragging && event.which < 2)) {
                    return;
                }

                if (!isTouch) {
                    // prevents native image dragging in Firefox
                    stopDefault(event);
                }

                // Reset dragging object
                continuousInit(source);

                // Properties used in dragHandler
                dragging.init = 0;
                dragging.$source = $(event.target);
                dragging.touch = isTouch;
                dragging.pointer = isTouch ? event.originalEvent.touches[0] : event;
                dragging.initX = dragging.pointer.pageX;
                dragging.initY = dragging.pointer.pageY;
                dragging.initPos = isSlidee ? pos.cur : hPos.cur;
                dragging.start = +new Date();
                dragging.time = 0;
                dragging.path = 0;
                dragging.delta = 0;
                dragging.locked = 0;
                dragging.history = [0, 0, 0, 0];
                dragging.pathToLock = isSlidee ? isTouch ? 30 : 10 : 0;

                // Bind dragging events
                $doc.on(isTouch ? dragTouchEvents : dragMouseEvents, dragHandler);

                // Pause ongoing cycle
                self.pause(1);

                // Add dragging class
                (isSlidee ? $slidee : $handle).addClass(o.draggedClass);

                // Trigger moveStart event
                trigger('moveStart');

                // Keep track of a dragging path history. This is later used in the
                // dragging release swing calculation when dragging SLIDEE.
                if (isSlidee) {
                    historyID = setInterval(draggingHistoryTick, 10);
                }
            }

            /**
             * Handler for dragging scrollbar handle or SLIDEE.
             *
             * @param  {Event} event
             *
             * @return {Void}
             */
            function dragHandler(event) {
                dragging.released = event.type === 'mouseup' || event.type === 'touchend';
                dragging.pointer = dragging.touch ? event.originalEvent[dragging.released ? 'changedTouches' : 'touches'][0] : event;
                dragging.pathX = dragging.pointer.pageX - dragging.initX;
                dragging.pathY = dragging.pointer.pageY - dragging.initY;
                dragging.path = sqrt(pow(dragging.pathX, 2) + pow(dragging.pathY, 2));
                dragging.delta = o.horizontal ? dragging.pathX : dragging.pathY;

                if (!dragging.released && dragging.path < 1) return;

                // We haven't decided whether this is a drag or not...
                if (!dragging.init) {
                    // If the drag path was very short, maybe it's not a drag?
                    if (dragging.path < o.dragThreshold) {
                        // If the pointer was released, the path will not become longer and it's
                        // definitely not a drag. If not released yet, decide on next iteration
                        return dragging.released ? dragEnd() : undefined;
                    }
                    else {
                        // If dragging path is sufficiently long we can confidently start a drag
                        // if drag is in different direction than scroll, ignore it
                        if (o.horizontal ? abs(dragging.pathX) > abs(dragging.pathY) : abs(dragging.pathX) < abs(dragging.pathY)) {
                            dragging.init = 1;
                        } else {
                            return dragEnd();
                        }
                    }
                }

                stopDefault(event);

                // Disable click on a source element, as it is unwelcome when dragging
                if (!dragging.locked && dragging.path > dragging.pathToLock && dragging.slidee) {
                    dragging.locked = 1;
                    dragging.$source.on(clickEvent, disableOneEvent);
                }

                // Cancel dragging on release
                if (dragging.released) {
                    dragEnd();

                    // Adjust path with a swing on mouse release
                    if (o.releaseSwing && dragging.slidee) {
                        dragging.swing = (dragging.delta - dragging.history[0]) / 40 * 300;
                        dragging.delta += dragging.swing;
                        dragging.tweese = abs(dragging.swing) > 10;
                    }
                }

                slideTo(dragging.slidee ? round(dragging.initPos - dragging.delta) : handleToSlidee(dragging.initPos + dragging.delta));
            }

            /**
             * Stops dragging and cleans up after it.
             *
             * @return {Void}
             */
            function dragEnd() {
                clearInterval(historyID);
                dragging.released = true;
                $doc.off(dragging.touch ? dragTouchEvents : dragMouseEvents, dragHandler);
                (dragging.slidee ? $slidee : $handle).removeClass(o.draggedClass);

                // Make sure that disableOneEvent is not active in next tick.
                setTimeout(function () {
                    dragging.$source.off(clickEvent, disableOneEvent);
                });

                // Normally, this is triggered in render(), but if there
                // is nothing to render, we have to do it manually here.
                if (pos.cur === pos.dest && dragging.init) {
                    trigger('moveEnd');
                }

                // Resume ongoing cycle
                self.resume(1);

                dragging.init = 0;
            }

            /**
             * Check whether element is interactive.
             *
             * @return {Boolean}
             */
            function isInteractive(element) {
                return ~$.inArray(element.nodeName, interactiveElements) || $(element).is(o.interactive);
            }

            /**
             * Continuous movement cleanup on mouseup.
             *
             * @return {Void}
             */
            function movementReleaseHandler() {
                self.stop();
                $doc.off('mouseup', movementReleaseHandler);
            }

            /**
             * Buttons navigation handler.
             *
             * @param  {Event} event
             *
             * @return {Void}
             */
            function buttonsHandler(event) {
                /*jshint validthis:true */
                stopDefault(event);
                switch (this) {
                    case $forwardButton[0]:
                    case $backwardButton[0]:
                        self.moveBy($forwardButton.is(this) ? o.moveBy : -o.moveBy);
                        $doc.on('mouseup', movementReleaseHandler);
                        break;

                    case $prevButton[0]:
                        self.prev();
                        break;

                    case $nextButton[0]:
                        self.next();
                        break;

                    case $prevPageButton[0]:
                        self.prevPage();
                        break;

                    case $nextPageButton[0]:
                        self.nextPage();
                        break;
                }
            }

            /**
             * Mouse wheel delta normalization.
             *
             * @param  {Event} event
             *
             * @return {Int}
             */
            function normalizeWheelDelta(event) {
                // wheelDelta needed only for IE8-
                scrolling.curDelta = ((o.horizontal ? event.deltaY || event.deltaX : event.deltaY) || -event.wheelDelta);
                scrolling.curDelta /= event.deltaMode === 1 ? 3 : 100;
                if (!itemNav) {
                    return scrolling.curDelta;
                }
                time = +new Date();
                if (scrolling.last < time - scrolling.resetTime) {
                    scrolling.delta = 0;
                }
                scrolling.last = time;
                scrolling.delta += scrolling.curDelta;
                if (abs(scrolling.delta) < 1) {
                    scrolling.finalDelta = 0;
                } else {
                    scrolling.finalDelta = round(scrolling.delta / 1);
                    scrolling.delta %= 1;
                }
                return scrolling.finalDelta;
            }

            /**
             * Mouse scrolling handler.
             *
             * @param  {Event} event
             *
             * @return {Void}
             */
            function scrollHandler(event) {
                // Mark event as originating in a Sly instance
                event.originalEvent[namespace] = self;
                // Don't hijack global scrolling
                var time = +new Date();
                if (lastGlobalWheel + o.scrollHijack > time && $scrollSource[0] !== document && $scrollSource[0] !== window) {
                    lastGlobalWheel = time;
                    return;
                }
                // Ignore if there is no scrolling to be done
                if (!o.scrollBy || pos.start === pos.end) {
                    return;
                }
                var delta = normalizeWheelDelta(event.originalEvent);
                // Trap scrolling only when necessary and/or requested
                if (o.scrollTrap || delta > 0 && pos.dest < pos.end || delta < 0 && pos.dest > pos.start) {
                    stopDefault(event, 1);
                }
                self.slideBy(o.scrollBy * delta);
            }

            /**
             * Scrollbar click handler.
             *
             * @param  {Event} event
             *
             * @return {Void}
             */
            function scrollbarHandler(event) {
                // Only clicks on scroll bar. Ignore the handle.
                if (o.clickBar && event.target === $sb[0]) {
                    stopDefault(event);
                    // Calculate new handle position and sync SLIDEE to it
                    slideTo(handleToSlidee((o.horizontal ? event.pageX - $sb.offset().left : event.pageY - $sb.offset().top) - handleSize / 2));
                }
            }

            /**
             * Keyboard input handler.
             *
             * @param  {Event} event
             *
             * @return {Void}
             */
            function keyboardHandler(event) {
                if (!o.keyboardNavBy) {
                    return;
                }

                switch (event.which) {
                    // Left or Up
                    case o.horizontal ? 37 : 38:
                        stopDefault(event);
                        self[o.keyboardNavBy === 'pages' ? 'prevPage' : 'prev']();
                        break;

                    // Right or Down
                    case o.horizontal ? 39 : 40:
                        stopDefault(event);
                        self[o.keyboardNavBy === 'pages' ? 'nextPage' : 'next']();
                        break;
                }
            }

            /**
             * Click on item activation handler.
             *
             * @param  {Event} event
             *
             * @return {Void}
             */
            function activateHandler(event) {
                /*jshint validthis:true */

                // Ignore clicks on interactive elements.
                if (isInteractive(this)) {
                    event.originalEvent[namespace + 'ignore'] = true;
                    return;
                }

                // Ignore events that:
                // - are not originating from direct SLIDEE children
                // - originated from interactive elements
                if (this.parentNode !== $slidee[0] || event.originalEvent[namespace + 'ignore']) return;

                self.activate(this);
            }

            /**
             * Click on page button handler.
             *
             * @param {Event} event
             *
             * @return {Void}
             */
            function activatePageHandler() {
                /*jshint validthis:true */
                // Accept only events from direct pages bar children.
                if (this.parentNode === $pb[0]) {
                    self.activatePage($pages.index(this));
                }
            }

            /**
             * Pause on hover handler.
             *
             * @param  {Event} event
             *
             * @return {Void}
             */
            function pauseOnHoverHandler(event) {
                if (o.pauseOnHover) {
                    self[event.type === 'mouseenter' ? 'pause' : 'resume'](2);
                }
            }

            /**
             * Trigger callbacks for event.
             *
             * @param  {String} name Event name.
             * @param  {Mixed}  argX Arguments passed to callbacks.
             *
             * @return {Void}
             */
            function trigger(name, arg1) {
                if (callbacks[name]) {
                    l = callbacks[name].length;
                    // Callbacks will be stored and executed from a temporary array to not
                    // break the execution queue when one of the callbacks unbinds itself.
                    tmpArray.length = 0;
                    for (i = 0; i < l; i++) {
                        tmpArray.push(callbacks[name][i]);
                    }
                    // Execute the callbacks
                    for (i = 0; i < l; i++) {
                        tmpArray[i].call(self, name, arg1);
                    }
                }
            }

            /**
             * Destroys instance and everything it created.
             *
             * @return {Void}
             */
            self.destroy = function () {
                // Remove the reference to itself
                Sly.removeInstance(frame);

                // Unbind all events
                $scrollSource
                    .add($handle)
                    .add($sb)
                    .add($pb)
                    .add($forwardButton)
                    .add($backwardButton)
                    .add($prevButton)
                    .add($nextButton)
                    .add($prevPageButton)
                    .add($nextPageButton)
                    .off('.' + namespace);

                // Unbinding specifically as to not nuke out other instances
                $doc.off('keydown', keyboardHandler);

                // Remove classes
                $prevButton
                    .add($nextButton)
                    .add($prevPageButton)
                    .add($nextPageButton)
                    .removeClass(o.disabledClass);

                if ($items && rel.activeItem != null) {
                    $items.eq(rel.activeItem).removeClass(o.activeClass);
                }

                // Remove page items
                $pb.empty();

                if (!parallax) {
                    // Unbind events from frame
                    $frame.off('.' + namespace);
                    // Restore original styles
                    frameStyles.restore();
                    slideeStyles.restore();
                    sbStyles.restore();
                    handleStyles.restore();
                    // Remove the instance from element data storage
                    $.removeData(frame, namespace);
                }

                // Clean up collections
                items.length = pages.length = 0;
                last = {};

                // Reset initialized status and return the instance
                self.initialized = 0;
                return self;
            };

            /**
             * Initialize.
             *
             * @return {Object}
             */
            self.init = function () {
                if (self.initialized) {
                    return;
                }

                // Disallow multiple instances on the same element
                if (Sly.getInstance(frame)) throw new Error('There is already a Sly instance on this element');

                // Store the reference to itself
                Sly.storeInstance(frame, self);

                // Register callbacks map
                self.on(callbackMap);

                // Save styles
                var holderProps = ['overflow', 'position'];
                var movableProps = ['position', 'webkitTransform', 'msTransform', 'transform', 'left', 'top', 'width', 'height'];
                frameStyles.save.apply(frameStyles, holderProps);
                sbStyles.save.apply(sbStyles, holderProps);
                slideeStyles.save.apply(slideeStyles, movableProps);
                handleStyles.save.apply(handleStyles, movableProps);

                // Set required styles
                var $movables = $handle;
                if (!parallax) {
                    $movables = $movables.add($slidee);
                    $frame.css('overflow', 'hidden');
                    if (!transform && $frame.css('position') === 'static') {
                        $frame.css('position', 'relative');
                    }
                }
                if (transform) {
                    if (gpuAcceleration) {
                        $movables.css(transform, gpuAcceleration);
                    }
                } else {
                    if ($sb.css('position') === 'static') {
                        $sb.css('position', 'relative');
                    }
                    $movables.css({ position: 'absolute' });
                }

                // Navigation buttons
                if (o.forward) {
                    $forwardButton.on(mouseDownEvent, buttonsHandler);
                }
                if (o.backward) {
                    $backwardButton.on(mouseDownEvent, buttonsHandler);
                }
                if (o.prev) {
                    $prevButton.on(clickEvent, buttonsHandler);
                }
                if (o.next) {
                    $nextButton.on(clickEvent, buttonsHandler);
                }
                if (o.prevPage) {
                    $prevPageButton.on(clickEvent, buttonsHandler);
                }
                if (o.nextPage) {
                    $nextPageButton.on(clickEvent, buttonsHandler);
                }

                // Scrolling navigation
                $scrollSource.on(wheelEvent, scrollHandler);

                // Clicking on scrollbar navigation
                if ($sb[0]) {
                    $sb.on(clickEvent, scrollbarHandler);
                }

                // Click on items navigation
                if (itemNav && o.activateOn) {
                    $frame.on(o.activateOn + '.' + namespace, '*', activateHandler);
                }

                // Pages navigation
                if ($pb[0] && o.activatePageOn) {
                    $pb.on(o.activatePageOn + '.' + namespace, '*', activatePageHandler);
                }

                // Dragging navigation
                $dragSource.on(dragInitEvents, { source: 'slidee' }, dragInit);

                // Scrollbar dragging navigation
                if ($handle) {
                    $handle.on(dragInitEvents, { source: 'handle' }, dragInit);
                }

                // Keyboard navigation
                $doc.on('keydown', keyboardHandler);

                if (!parallax) {
                    // Pause on hover
                    $frame.on('mouseenter.' + namespace + ' mouseleave.' + namespace, pauseOnHoverHandler);
                    // Reset native FRAME element scroll
                    $frame.on('scroll.' + namespace, resetScroll);
                }

                // Mark instance as initialized
                self.initialized = 1;

                // Load
                load(true);

                // Initiate automatic cycling
                if (o.cycleBy && !parallax) {
                    self[o.startPaused ? 'pause' : 'resume']();
                }

                // Return instance
                return self;
            };
        }

        Sly.getInstance = function (element) {
            return $.data(element, namespace);
        };

        Sly.storeInstance = function (element, sly) {
            return $.data(element, namespace, sly);
        };

        Sly.removeInstance = function (element) {
            return $.removeData(element, namespace);
        };

        /**
         * Return type of the value.
         *
         * @param  {Mixed} value
         *
         * @return {String}
         */
        function type(value) {
            if (value == null) {
                return String(value);
            }

            if (typeof value === 'object' || typeof value === 'function') {
                return Object.prototype.toString.call(value).match(/\s([a-z]+)/i)[1].toLowerCase() || 'object';
            }

            return typeof value;
        }

        /**
         * Event preventDefault & stopPropagation helper.
         *
         * @param {Event} event     Event object.
         * @param {Bool}  noBubbles Cancel event bubbling.
         *
         * @return {Void}
         */
        function stopDefault(event, noBubbles) {
            event.preventDefault();
            if (noBubbles) {
                event.stopPropagation();
            }
        }

        /**
         * Disables an event it was triggered on and unbinds itself.
         *
         * @param  {Event} event
         *
         * @return {Void}
         */
        function disableOneEvent(event) {
            /*jshint validthis:true */
            stopDefault(event, 1);
            $(this).off(event.type, disableOneEvent);
        }

        /**
         * Resets native element scroll values to 0.
         *
         * @return {Void}
         */
        function resetScroll() {
            /*jshint validthis:true */
            this.scrollLeft = 0;
            this.scrollTop = 0;
        }

        /**
         * Check if variable is a number.
         *
         * @param {Mixed} value
         *
         * @return {Boolean}
         */
        function isNumber(value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        }

        /**
         * Parse style to pixels.
         *
         * @param {Object}   $item    jQuery object with element.
         * @param {Property} property CSS property to get the pixels from.
         *
         * @return {Int}
         */
        function getPx($item, property) {
            return 0 | round(String($item.css(property)).replace(/[^\-0-9.]/g, ''));
        }

        /**
         * Make sure that number is within the limits.
         *
         * @param {Number} number
         * @param {Number} min
         * @param {Number} max
         *
         * @return {Number}
         */
        function within(number, min, max) {
            return number < min ? min : number > max ? max : number;
        }

        /**
         * Saves element styles for later restoration.
         *
         * Example:
         *   var styles = new StyleRestorer(frame);
         *   styles.save('position');
         *   element.style.position = 'absolute';
         *   styles.restore(); // restores to state before the assignment above
         *
         * @param {Element} element
         */
        function StyleRestorer(element) {
            var self = {};
            self.style = {};
            self.save = function () {
                if (!element || !element.nodeType) return;
                for (var i = 0; i < arguments.length; i++) {
                    self.style[arguments[i]] = element.style[arguments[i]];
                }
                return self;
            };
            self.restore = function () {
                if (!element || !element.nodeType) return;
                for (var prop in self.style) {
                    if (self.style.hasOwnProperty(prop)) element.style[prop] = self.style[prop];
                }
                return self;
            };
            return self;
        }

        // Local WindowAnimationTiming interface polyfill
        (function (w) {
            rAF = w.requestAnimationFrame
                || w.webkitRequestAnimationFrame
                || fallback;

            /**
            * Fallback implementation.
            */
            var prev = new Date().getTime();
            function fallback(fn) {
                var curr = new Date().getTime();
                var ms = Math.max(0, 16 - (curr - prev));
                var req = setTimeout(fn, ms);
                prev = curr;
                return req;
            }

            /**
            * Cancel.
            */
            var cancel = w.cancelAnimationFrame
                || w.webkitCancelAnimationFrame
                || w.clearTimeout;

            cAF = function(id){
                cancel.call(w, id);
            };
        }(window));

        // Feature detects
        (function () {
            var prefixes = ['', 'Webkit', 'Moz', 'ms', 'O'];
            var el = document.createElement('div');

            function testProp(prop) {
                for (var p = 0, pl = prefixes.length; p < pl; p++) {
                    var prefixedProp = prefixes[p] ? prefixes[p] + prop.charAt(0).toUpperCase() + prop.slice(1) : prop;
                    if (el.style[prefixedProp] != null) {
                        return prefixedProp;
                    }
                }
            }

            // Global support indicators
            transform = testProp('transform');
            gpuAcceleration = testProp('perspective') ? 'translateZ(0) ' : '';
        }());

        // Expose class globally
        w[className] = Sly;

        // jQuery proxy
        $.fn[pluginName] = function (options, callbackMap) {
            var method, methodArgs;

            // Attributes logic
            if (!$.isPlainObject(options)) {
                if (type(options) === 'string' || options === false) {
                    method = options === false ? 'destroy' : options;
                    methodArgs = Array.prototype.slice.call(arguments, 1);
                }
                options = {};
            }

            // Apply to all elements
            return this.each(function (i, element) {
                // Call with prevention against multiple instantiations
                var plugin = Sly.getInstance(element);

                if (!plugin && !method) {
                    // Create a new object if it doesn't exist yet
                    plugin = new Sly(element, options, callbackMap).init();
                } else if (plugin && method) {
                    // Call method
                    if (plugin[method]) {
                        plugin[method].apply(plugin, methodArgs);
                    }
                }
            });
        };

        // Default options
        Sly.defaults = {
            slidee:     null,  // Selector, DOM element, or jQuery object with DOM element representing SLIDEE.
            horizontal: false, // Switch to horizontal mode.

            // Item based navigation
            itemNav:        null,  // Item navigation type. Can be: 'basic', 'centered', 'forceCentered'.
            itemSelector:   null,  // Select only items that match this selector.
            smart:          false, // Repositions the activated item to help with further navigation.
            activateOn:     null,  // Activate an item on this event. Can be: 'click', 'mouseenter', ...
            activateMiddle: false, // Always activate the item in the middle of the FRAME. forceCentered only.

            // Scrolling
            scrollSource: null,  // Element for catching the mouse wheel scrolling. Default is FRAME.
            scrollBy:     0,     // Pixels or items to move per one mouse scroll. 0 to disable scrolling.
            scrollHijack: 300,   // Milliseconds since last wheel event after which it is acceptable to hijack global scroll.
            scrollTrap:   false, // Don't bubble scrolling when hitting scrolling limits.

            // Dragging
            dragSource:    null,  // Selector or DOM element for catching dragging events. Default is FRAME.
            mouseDragging: false, // Enable navigation by dragging the SLIDEE with mouse cursor.
            touchDragging: false, // Enable navigation by dragging the SLIDEE with touch events.
            releaseSwing:  false, // Ease out on dragging swing release.
            swingSpeed:    0.2,   // Swing synchronization speed, where: 1 = instant, 0 = infinite.
            elasticBounds: false, // Stretch SLIDEE position limits when dragging past FRAME boundaries.
            dragThreshold: 3,     // Distance in pixels before Sly recognizes dragging.
            interactive:   null,  // Selector for special interactive elements.

            // Scrollbar
            scrollBar:     null,  // Selector or DOM element for scrollbar container.
            dragHandle:    false, // Whether the scrollbar handle should be draggable.
            dynamicHandle: false, // Scrollbar handle represents the ratio between hidden and visible content.
            minHandleSize: 50,    // Minimal height or width (depends on sly direction) of a handle in pixels.
            clickBar:      false, // Enable navigation by clicking on scrollbar.
            syncSpeed:     0.5,   // Handle => SLIDEE synchronization speed, where: 1 = instant, 0 = infinite.

            // Pagesbar
            pagesBar:       null, // Selector or DOM element for pages bar container.
            activatePageOn: null, // Event used to activate page. Can be: click, mouseenter, ...
            pageBuilder:          // Page item generator.
                function (index) {
                    return '<li>' + (index + 1) + '</li>';
                },

            // Navigation buttons
            forward:  null, // Selector or DOM element for "forward movement" button.
            backward: null, // Selector or DOM element for "backward movement" button.
            prev:     null, // Selector or DOM element for "previous item" button.
            next:     null, // Selector or DOM element for "next item" button.
            prevPage: null, // Selector or DOM element for "previous page" button.
            nextPage: null, // Selector or DOM element for "next page" button.

            // Automated cycling
            cycleBy:       null,  // Enable automatic cycling by 'items' or 'pages'.
            cycleInterval: 5000,  // Delay between cycles in milliseconds.
            pauseOnHover:  false, // Pause cycling when mouse hovers over the FRAME.
            startPaused:   false, // Whether to start in paused sate.

            // Mixed options
            moveBy:        300,     // Speed in pixels per second used by forward and backward buttons.
            speed:         0,       // Animations speed in milliseconds. 0 to disable animations.
            easing:        'swing', // Easing for duration based (tweening) animations.
            startAt:       null,    // Starting offset in pixels or items.
            keyboardNavBy: null,    // Enable keyboard navigation by 'items' or 'pages'.

            // Classes
            draggedClass:  'dragged', // Class for dragged elements (like SLIDEE or scrollbar handle).
            activeClass:   'active',  // Class for active items and pages.
            disabledClass: 'disabled' // Class for disabled navigation elements.
        };
    }(jQuery, window));    
    
    // Load all Activities via ajaxRequest
    
    $base = window.location.protocol + '//' + window.location.hostname + '/' + window.location.pathname.split('/')[1] + '/';
    
    var process = $base + 'includes/get_resources.ajax.php';    
    var id = $('.program_id').attr('id');
   
    $('.slice').each(function() {
        
        $this = $(this);
        
        $level = $(this).children('.slice-level').attr('id');
        $unit = $(this).children('.slice-unit').attr('id');
        $lesson = $('.lesson').attr('id');
        $passport = $('.passport').attr('id');
        
        ajaxRequest( process, id, $level, $unit, $this, $lesson, $passport );
        
    });    
    
    // Hide/Show Levels and Reload Sly Frame
    
    $('.level-1').addClass('level-selected');
    $('.units-level-1').show();
    $('.slice-level-1').show();
    
    $('.levels>div').click(function() {
        $('.levels>div').removeClass('level-selected');
        $levelSelected = $(this).attr('class').split(' ')[2];
        
        $('.units p').hide();
        $('.units-' + $levelSelected).show();
        
        $('.slice').hide();
        $('.slice-' + $levelSelected).show();
        
        $(this).addClass('level-selected');
        
        $('.frame').sly('reload');
    });
    
    // Scroll to Unit Function
    
    $('.unit-link').click(function(e){
        $unitSelected = $(this).attr('class').split(' ')[1];
        $top = $('.slice.' + $unitSelected).offset().top;
        $("html").stop().scrollTo( { 
            top: $top,
            left: 0
        }, 1000 );
        e.preventDefault();
    });   

    // To Top Scroll Function
    
    $(document).scroll(function () {
        var yx = $(this).scrollTop();
        if (yx > 200) {
            $('#totop').css('opacity','1');
        } else {
            $('#totop').css('opacity','0');
        }

    });

    $('#totop').click(function(){$("html").stop().scrollTo( { top:10,left:0} , 1000 );});
    
    // Filter Activities by Lesson
    
    $(document).on('change','.select-lesson select', function() {
        $lu = $(this).attr('class').split('-')[0];
        $lesson = $(this).val();
        if( $lesson == 'choose' ) {
            $(this).parent().removeClass('lessonFilterSelected');
            $('.' + $lu + '-interaction_type').val('choose').change().parent().hide();
            $('.' + $lu + 'frame .lesson').parent().parent().parent().parent().removeClass('hiddenlesson');
        } else {
            $(this).parent().addClass('lessonFilterSelected');
            $('.' + $lu + '-interaction_type').parent().show();
            $('.' + $lu + 'frame .lesson').parent().parent().parent().parent().addClass('hiddenlesson');
            $('.' + $lu + 'frame .lesson#' + $lesson + ',.' + $lu + 'frame .lesson#0').parent().parent().parent().parent().removeClass('hiddenlesson');
        }
    });
    
    // Filter Activities by Interaction Type
    
    $(document).on('change','.select-interaction_type select', function() {
        $intType =  $(this).val();
        if( $intType == 'choose' ) {
            $('.' + $lu + 'frame .interaction_type').parent().parent().parent().parent().removeClass('hiddeninteraction_type');
        } else {
            $('.' + $lu + 'frame .interaction_type').parent().parent().parent().parent().addClass('hiddeninteraction_type');
            $('.' + $lu + 'frame .interaction_type#' + $intType).parent().parent().parent().parent().removeClass('hiddeninteraction_type');              
        }
    });
    
    // Mark Activity Type (Book Cover) as unavailable if all activities have been filtered out
    
    $(document).on('change','.resources select', function() {
        $lu = $(this).attr('class').split('-')[0];
        $thisFrame = '.' + $(this).parent().parent().parent().attr('class').split(' ')[2];
        $( $thisFrame + ' .expanded,' + $thisFrame + ' .collapsed').each(function() {
            $x = $(this).attr('class').split(' ')[0];
            $length = $('.' + $x + '.resource_individuals').not('.hiddenlesson,.hiddeninteraction_type,.hiddenAssignable').length;
            if( $length == '0' ) {
                $(this).addClass('noneAvailable');
            } else {
                $(this).removeClass('noneAvailable');
            }
        });
        $('.' + $lu + 'frame').sly('reload');
    });
    
    // Collapse Activity Type (Book Cover) and Resize Slider
    
    $(document).on('click', '.collapsed', function() {

        $(this).removeClass('collapsed').addClass('expanded');

        $resource = '.' + $(this).attr('class').split(' ')[0];
        $frame = '.' + $(this).parent().parent().attr('class').split(' ')[0];

        $($resource + '.resource_individuals').show();
        $($frame).sly('reload');

    });

    // Expand Activity Type (Book Cover) and Resize Slider
    
    $(document).on('click', '.expanded', function() {

        $(this).removeClass('expanded').addClass('collapsed');

        $resource = '.' + $(this).attr('class').split(' ')[0];
        $frame = '.' + $(this).parent().parent().attr('class').split(' ')[0];

        $($resource + '.resource_individuals').hide();
        $($frame).sly('reload');

    });
    
    // Launch iCulture Modal from Slider
    
    $(document).on('click', '.resource_item.iculture', function(e) {
        $iCURL = $(this).find('.resource-meta-data.iculture-url').attr('id');
        $iCtype = $(this).find('.resource-meta-data.iculture-type').attr('id');
        $iClang = $(this).find('.resource-meta-data.iculture-lang').attr('id');
        $iCcontent = '<iframe scrolling="no" src="' + $iCURL + '" frameborder="0"></iframe><div class="modalClose anim"></div>';
        
        $('.modalContainer').addClass('iCultureModal iCultureModal' + $iCtype + ' iCultureModal' + $iClang).append($iCcontent).parent().fadeIn(300);
        
        e.preventDefault(); 
    });
    
    // Launch iCulture Modal from Modal Container
    
    $(document).on('click', '.cover-iculturelarge', function() {
        $iCURL = $(this).parent().find('.resource-meta-data.iculture-url').attr('id');
        $iCtype = $(this).parent().find('.resource-meta-data.iculture-type').attr('id');
        $iClang = $(this).parent().find('.resource-meta-data.iculture-lang').attr('id');
        $iCcontent = '<iframe scrolling="no" src="' + $iCURL + '" frameborder="0"></iframe><div class="modalClose anim"></div>';
        
        $(this).parent().parent().fadeOut(300, function() {
            $('.modalContainer').html('').addClass('iCultureModal iCultureModal' + $iCtype + ' iCultureModal' + $iClang).append($iCcontent).parent().fadeIn(300);
        });
        
    });
    
    // Close iCulture Modal
    
    $(document).on('click','.iCultureModal .modalClose', function() {
        modalClose();
    });
        
    // Launch Activity Modal Window from Info Icon  
    
    $(document).on('click', '.resource_item .info_icon', function(e) {
        
        $id = $(this).parent().find('.resource-meta-data.activity_id').attr('id');
        getModalData($id);
        
        e.preventDefault();
    });
    
    // Launch Activity Modal Window from iCulture Info Icon
    
    $(document).on('click', '.iculture_info_icon', function(e) {
        
        $id = $(this).parent().find('.resource-meta-data.activity_id').attr('id');
        getModalData($id);
        
        e.preventDefault();
    });
    
    // Launch Activity Preview from Info Icon (Scheduling Queue)
    
    $(document).on('click', '.queue-label .info_icon', function() {
        $id = $(this).parent().attr('id').split('-')[2];
        closeScheduling();
        getModalData($id);
    });
    
    // Close Modal Container
        
    $(document).on('click', '.modalClose', function() {
        modalClose();
    });
    
    // Launch Activity Preview from Activity Info Modal
    
    $(document).on('click', '.modalContainer .resource-cover', function(e) {
        
        $id = $(this).parent().parent().find('.resource-meta-data.activity_id').attr('id');
        $a = $(this).parent().parent().find('.resource-meta-data.assignable').attr('id');
        launchActivityPreview($id,$a,e);
        setTimeout(function() {
            overlayCheck($a);
        }, 1500);
        
    });
    
    // Close Bookshelf Interaction Modal and Open Modal Container
    
    $(document).on('click', '.ui-dialog-titlebar-close, .ui-widget-overlay', function() {
        modalLaunch();
    });
    
    var modal = '<div class="modalContainer anim"></div>';
    
    // Click to Left Arrow in Modal Container
    
    $(document).on('click', '.modalContainer .left-arrow', function() {
        $id = $(this).parent().find('.resource-meta-data.activity_id').attr('id');
        $next = getNextActivityID($id,'1');
        
        $(this).parent().addClass('left-clicked').delay(300).queue(function() {
            $(this).removeClass('left-clicked').addClass('right-clicked').delay(300).queue(function() {
                $(this).removeClass('right-clicked').dequeue();
                
                getModalData($next);
            }).dequeue(); 
            $('.modalContainer').html('');
        });
    });
    
    // Click to Right Arrow in Modal Container
    
    $(document).on('click', '.modalContainer .right-arrow', function() {
        $id = $(this).parent().find('.resource-meta-data.activity_id').attr('id');
        $next = getNextActivityID($id,'0');
        
        $(this).parent().addClass('right-clicked').delay(300).queue(function() {
            $(this).removeClass('right-clicked').addClass('left-clicked').delay(300).queue(function() {
                $(this).removeClass('left-clicked').dequeue();
                
                getModalData($next);
            }).dequeue(); 
            $('.modalContainer').html('');
        });
    });
    
    // Click to Add Activity to Scheduling Queue
    
    $(document).on('click', '.queue-button', function(e) {
        $id = $(this).parent().find('.resource-meta-data.activity_id').attr('id');
        addToQueue($id);
        if( typeof $uid != 'undefined' ) {
            writeStoredActivities($uid,formatAct());
        }
    });
    
    // Click to Remove Activity from Scheduling Queue
    
    $(document).on('click', '.scheduling-edit', function() {
        $id = $(this).parent().find('.queue-label').attr('id').split('-')[2];
        removeFromQueue($id);
        if( typeof $uid != 'undefined' ) {
            writeStoredActivities($uid,formatAct());
        }
    });
    
    // Close Scheduling Queue Modal
    
    $(document).on('click', '.schedulingClose', function() {
        schedulingClose(); 
    });
    
    // Add Header Row to Scheduling Queue Table
    
    $unitLabel = $('.unit-title').text().split(' ')[0];
    $lessonLabel = $('.lesson').attr('id');
    $('.scheduling-container table').append('<tr class="header-row"><td class="scheduling-row-title" id="sort-label">Title<div class="sort-arrows anim"><div class="arrow-up"></div><div class="arrow-down"></div></div></td><td class="center" id="sort-level">Level<div class="sort-arrows anim"><div class="arrow-up"></div><div class="arrow-down"></div></div></td><td class="center" id="sort-unit">' + $unitLabel + '<div class="sort-arrows anim"><div class="arrow-up"></div><div class="arrow-down"></div></div></td><td class="center" id="sort-lesson">' + $lessonLabel + '<div class="sort-arrows anim"><div class="arrow-up"></div><div class="arrow-down"></div></div></td><td class="center assign-all">Assign</td><td class="center remove-all">Remove</td</tr>');
    
    // Open Scheduling Queue Modal
    
    $(document).on('click', '.scheduling-icon', function(e) {
        modalClose();
        bodyFixed();
        $('.scheduling-queue').fadeIn();
    });
    
    // Remove All Items from Scheduling Queue
    
    $(document).on('click', '.remove-all', function() {
        launchConfirmPrompt();
    });
    $(document).on('click', '.confirm-no', function() {
        closeConfirmPrompt();
    });
    $(document).on('click', '.confirm-yes', function() {
        closeConfirmPrompt();
        $('.scheduled-item').each(function() {
            $id = $(this).find('.queue-label').attr('id').split('-')[2];
            removeFromQueue($id);
        });
        if( typeof $uid != 'undefined' ) {
            writeStoredActivities($uid,formatAct());
        }
    });
    
    // Click on Proceed to "Assign" Button in Scheduling Queue
    
    $(document).on('click', '.scheduling-assign', function() {
        if( !$(this).hasClass('assignDeactivated') ) {
            $schedArray = [];
            $('td input[type=checkbox]:checked').parent().parent().each(function() {
                $schedArray.push({
                    Book_ID: $(this).find('.book_id').text(),
                    Interaction_ID: $(this).find('.interaction_id').text()
                });
                $id = $(this).find('.queue-label').attr('id').split('-')[2];
                removeFromQueue($id);
            });
            if( typeof $uid != 'undefined' ) {
                writeStoredActivities($uid,formatAct());
            }
            console.log($schedArray);
        }
    });
    
    // Filter by Assignable Activities
    
    $(document).on('change', '.select-assignable select', function() {
        $aV = $('.select-assignable select').val();
        if( $aV == 'choose' || $aV == '0' ) {
            $('.resource_individuals').each(function() {
                $(this).removeClass('hiddenAssignable');
            });
            $('.expanded, .collapsed').removeClass('noneAvailable');
        } else if ( $aV == '1' ) {
            
            showLoading();
            setTimeout(filterByAssignable, 500);
            
        }
        $('.frame').sly('reload');
    });
    
    // Check/Uncheck All Checkboxes in Scheduling Queue
    
    $(document).on('change', '.selectAll input', function() {
        checkAll(); 
    });
    
    // De/Activate Assign Button Based on Available Items to Assign (checked)
    
    $(document).on('change', '.scheduling-queue input', function() {
        $i = $('.scheduling-queue input:checked').length;
        if( $i == 0 ) {
            $('.scheduling-assign').addClass('assignDeactivated');
        } else {
            $('.scheduling-assign').removeClass('assignDeactivated');
        }
    });
    
    // Sort Scheduling Queue Table
    
    $(document).on('click', '.sort-arrows', function() {
        $sort = $(this).parent().attr('id').split('-')[1];
        rewriteSchedQueue($sort);
    });
    
    // Bookshelf Login Button Click
    
    $(document).on('click', '.overlayCheckYes', function() {
        
        window.open('https://emc.bookshelf.emcp.com/account', '_blank');
        
        $('.overlayCheckYes').parent().append('<div class="confirm-button overlayCheckClose">OK! I\'m logged in!</div>')
        $('.overlayCheckYes').remove();
        
    });
    
     $(document).on('click', '.overlayCheckClose', function() {
        
        $(this).parent().parent().fadeOut( function() {
            $(this).remove(); 
        });
         
        launchBookshelfBehindScenes();
        $bookshelfFailSafe = '1';
        
        bodyScroll();
    });   
    
    // Keyboard Shortcuts
    
    $(document).keydown(function(e) {
        
        key = e.which;
        
        switch(key) {
            case 37:
                $('.modalContainer .left-arrow').click();
                break;
            case 39:
                $('.modalContainer .right-arrow').click();
                break;
            case 13: 
                $('.modalContainer .queue-button').click();
                break;
        }
    });
    
    if( typeof $uid != 'undefined' ) {
        getStoredActivities($uid);
    }
    
});

// FUNCTION AJAX Request to get all resouces (a = URL, b = activity_id, c = level, d = unit, e = $(this), f = lesson, g = Passport {TRUE/FALSE})

function ajaxRequest(a,b,c,d,e,f,g) {
    $.ajax({
        url: a,
        async: true,
        type: "POST",
        data: {
            id: b,
            level: c,
            unit: d,
            lesson: f,
            passport: g
        }
    }).done(function(data) {
        e.children('.resources').append(data); 
    }); 
    return;   
}

// FUNCTION AJAX Get Stored Activities (u = uid)

function getStoredActivities(u) {
    $.ajax({
        url: $base + 'includes/get_user_activities.ajax.php',
        async: true,
        type: "POST",
        data: {
            uid: u
        }
    }).done(function(data) {
        if( data != '' ) {
            $a = data.split(',');
            $l = $a.length;
            for( var i = 0; i < $l; i++ ) {
                addToQueue($a[i]);
            }
        }
    });
    return;
}

// FUNCTION AJAX Write Stored Activities (u = uid, a = activities)

function writeStoredActivities(u,a) {
    $.ajax({
        url: $base + 'includes/write_user_activities.ajax.php',
        async: true,
        type: "POST",
        data: {
            uid: u,
            act: a
        }
    });
    return;
    
}

// FUNCTION Format activity_ids to be stored

function formatAct() {
    $act = '';
    $('.scheduling-queue .queue-label').each(function() {
        $act += $(this).attr('id').split('-')[2] + ',';
    });
    $act = $act.substring(0, $act.length - 1);
    return $act;
}

// FUNCTION Mobile Check

function isMobile(){
    return (
        (navigator.platform.indexOf("iPhone") != -1) ||
        (navigator.platform.indexOf("iPod") != -1) ||
        (navigator.platform.indexOf("iPad") != -1) ||
        (navigator.platform.indexOf("Android") != -1)
    );
}

// FUNCTION Launch Modal Container (i = activity_id)
    
function getModalData(i) {
    
    $('.modalContainer').html('');
    
    a = $('.resource-meta-data.activity_id-' + i).parent();
    
    b = a.find('.resource-meta-data.type').attr('id');
    c = a.parent().find('.resource_modal_info').html();
    d = a.find('.resource-meta-data.level').attr('id');
    e = a.find('.resource-meta-data.url').attr('id');
    f = 'cover-' + b + 'l' + d;
    
    if( b == 'iculture' ) {
        f = 'cover-iculturelarge';
        g = '<div class="resource-cover ' + f + '"></div>';
    } else {
        g = '<a href="' + e + '" target="_blank"><div class="resource-cover ' + f + '"></div></a>';
    }
    
    bodyFixed();
    $('.modalContainer').append(g + c).parent().fadeIn(300);
    
    return;
}

// FUNCTION Launch/Close Modal Container (i = {0:reset, 1:preserve})

function modalLaunch() {
    $('.modalBackground').fadeIn();
    bodyFixed();
    return;
}
function modalClose(i) {
    if( i == '1' ) { 
        $('.modalBackground').fadeOut();
    } else {
        $('.modalBackground').fadeOut(300, function() {
            $('.modalContainer').remove();
            $(this).append('<div class="modalContainer anim"></div>');
        });
    }
    bodyScroll();
    return;
}

// FUNCTION Lauch/Close Scheduling Queue

function launchScheduling() {
    $('.scheduling-queue').fadeIn();
    bodyFixed();
    return;
}
function closeScheduling() {
    $('.scheduling-queue').fadeOut();
    bodyScroll();
    return;
}

// FUNCTION Bookshelf Login Check

$bookshelfFailSafe = '0';
function overlayCheck($v) {
    
    if( $v == 1 ) {
        
        if( $bookshelfFailSafe != '1' ) {
    
            if( $('.ui-widget-overlay ').length == 1 ) {
                return;
            } else {

                bodyFixed();
                $('<div class="confirm-background"><div class="confirm-container overlay-container"><h2 class="text-centered">Login Required</h2><p>To preview activities from your eBook here, you first need to login.</p><div class="confirm-button overlayCheckYes">Login to Bookshelf</div></div></div>' ).hide().appendTo('body').fadeIn();

                return;
            }
            
        } else {
            return;
        }
        
    } else {
        return;
    }
}

// FUNCTION Launch Bookshelf iFrame to Guarantee Session Initiated

function launchBookshelfBehindScenes() {
    
    $('.bookshelfIframe').remove();
    
    var $iframeURL = $('.student_ebook:first').find('.resource_modal_info .resource-meta-data.url').attr('id');
    $('body').append('<iframe src="' + $iframeURL + '" class="bookshelfIframe"></iframe>');
    
    return;

}

// FUNCTION Launch Activity Preview Overlay (i = activity_id, a = {1:assignable}, e = event)

function launchActivityPreview(i,a,e) {
    
    if( !isMobile() ) {
        
        if( a == '1' ) {
            
            $('.BShelf, .ui-widget-overlay, .ui-dialog, .dialog-container, .interaction-trigger').remove();

            $this = $('.resource-meta-data.activity_id-' + i).parent();

            $int_type = $this.find('.resource-meta-data.interaction_type').attr('id');
            $int_num = $this.find('.resource-meta-data.interaction_num').attr('id');
            $int_id = $int_type + '.' + $int_num;

            $BSURL = $this.find('.resource-meta-data.url').attr('id').split('#')[0];
            $BSjsURL = $BSURL + 'OEBPS/Data/' + $this.find('.resource-meta-data.page').attr('id') + '.js';

            $.ajax({
                url: $BSjsURL,
                dataType: 'script'
            }).done(function() {
                
                    $('<script></script>', {
                        'class': 'BShelf',
                        'src': $base + 'js/app.js'
                    }).appendTo('head');


                    for (i = 0; i < window.pageData.interactions.length; i++) { 
                        if (pageData.interactions[i].id == $int_id) {
                            pageData.interactions[i].callback();
                            break;
                        }
                    }

                    if( $('audio').length ) {
                        $('audio').each(function() {
                            $relAudio = $(this).attr('src');
                            $actAudio = $BSURL + 'OEBPS' + $relAudio.replace('..', '');
                            $(this).attr('src',$actAudio);
                        });
                    }

                });

            e.preventDefault();

            modalClose('1');

        }

    }    
    return;
}

// FUNCTION Close Scheduling Modal

function schedulingClose() {
    $('.scheduling-queue').fadeOut();
    bodyScroll();
    return;
}

// FUNCTION Get the Next Activity ID (i = activity_id, y = direction {1:left, 0:right} )

function getNextActivityID(i,y) {
    
    m = $('.resource_modal_info .resource-meta-data.activity_id-' + i);
    a = m.parent().parent().parent().parent().parent().parent();
    b = m.parent().find('.resource-meta-data.type').attr('id');
    c = a.attr('class').split(' ')[0];
    d = [];
    
    $('.' + c + ' .type#' + b).parent().parent().parent().parent().not('.hiddenlesson,.hiddeninteraction_type,.hiddenAssignable').find('.resource-meta-data.activity_id').each(function() {
        d.push( $(this).attr('id') );  
    });
    
    x = 0;
    while( x < d.length ) {
        if( i == d[x] ) {
            
            if( y == '0' ) {
                z = x + 1;
            } else {
                z = x - 1;   
            }
            
            if( z == d.length ) {
                z = '0';
            } else if ( z == '-1' ) {
                z = d.length - 1;   
            }
            break;
        }
        
        x++;
    }
    
    return d[z];
    
}

// FUNCTION Add Activity to Scheduling Queue (i = activity_id)

function addToQueue(i) {
    
    $this = $('.resource_modal_info .resource-meta-data.activity_id-' + i).parent();
    
    if( $this.find('.queue-button').hasClass('queue-added') ) {
        // Do 
        return false;
    } else {
        
        $level = $this.find('.resource-meta-data.level').attr('id');
        $unit = $this.find('.resource-meta-data.unit').attr('id');
        $schedLesson = $this.find('.resource-meta-data.lesson').attr('id');
        $label = $this.find('.resource-meta-data.label').text();
        $book_id = $this.find('.resource-meta-data.book_id').attr('id');
        $interaction_id = $this.find('.resource-meta-data.interaction_type').attr('id') + '.' + $this.find('.resource-meta-data.interaction_num').attr('id');
        $labelTrimmed = $label.replace('Activity Name: ', '');

        $this.find('.queue-button').addClass('queue-added').text('Added!');
        $('.modalContainer .queue-button').addClass('queue-added').text('Added!');

        $('.scheduling-container table').append('<tr class="scheduled-item"><td class="hidden book_id">' + $book_id + '</td><td class="hidden interaction_id">' + $interaction_id +'</td><td class="queue-label scheduling-row-title" id="activity-id-' + i + '">' + $labelTrimmed + '<div class="info_icon"></div></td><td class="queue-level center" id="' + $level + '">' + $level + '</td><td class="queue-unit center" id="' + $unit + '">' + $unit + '</td><td class="queue-lesson center" id="' + $schedLesson + '">' + $schedLesson + '</td><td class="center scheduling-select"><input type="checkbox" value="1" id="' + i  + '" name="check" checked="checked" /><label for="' + i  + '"></label></td><td class="center scheduling-edit"></td></tr>');

        $('.scheduling-icon').fadeIn();
        getSchedulingQueue();
        return true;
            
    }
}

// FUNCTION Remove Activity from Scheduling Queue (i = activity_id)

function removeFromQueue(i,x) {
    $('.queue-label.scheduling-row-title#activity-id-' + i).parent().remove();
    $('.resource_individuals .activity_id-' + i).parent().find('.queue-button').removeClass('queue-added').text('Add to Scheduling Queue');
    
    if( $('.scheduling-container tr').length == 1 && !x ) {
        schedulingClose();
        bodyScroll();
        $('.scheduling-icon').fadeOut();
    }
    
    getSchedulingQueue();
    return;
}

// FUNCTION Remove/Restore Scrolling to Window

function bodyFixed() {
    $('body').addClass('fixed');
    return;
}
function bodyScroll() {
    $('body').removeClass('fixed');
    return;
}

// FUNCTION Launch/Close Confirm Prompt

function launchConfirmPrompt() {
    $('.confirm-background').fadeIn();
    return;
}
function closeConfirmPrompt() {
    $('.confirm-background').fadeOut();
    return;
}

// FUNCTION Filter by Assignable vs. Non-Assignable

function filterByAssignable() {    
    $('.expanded, .collapsed').each(function() {
        $actGroup = '.' + $(this).attr('class').split(' ')[0];
        $count = $($actGroup).length - 1;
        $i = 0;

        $($actGroup + '.resource_individuals').each(function() {
            $v = $(this).find('.resource-meta-data.assignable').attr('id');
            if( $v == '0' ) {
                $(this).addClass('hiddenAssignable');
                $i++;
                if( $i == $count ) {
                    $($actGroup + '.expanded, ' + $actGroup + '.collapsed').addClass('noneAvailable');
                }
            }
        });

    });
    hideLoading();
    return;
}

// FUNCTION Loading Placeholder

function showLoading() {
    $('.loading').show();
    bodyFixed();
    return;
}
function hideLoading() {
    $('.loading').fadeOut();
    bodyScroll();
    return;
}

// FUNCTION Get Scheduling Queue Array
var $schedIDs = [];
function getSchedulingQueue() {
    $schedIDs = [];
    $('.scheduling-queue').find('.queue-label').each(function() {
        $id = $(this).attr('id').split('-')[2];
        $schedIDs.push($id);
    });
    return;
}

// FUNCTION Check All Items in Scheduling Queue

function checkAll() {
    $j = $('.selectAll input:checked').length;
    if( $j > 0 ) {
        $('input').prop('checked',true);
    } else {
        $('input').prop('checked',false);
    }
    return;
}

// FUNCTION Generate Array from Pending Assignments table

function genSchedQueueArray() {
    
    $schedQueueArray = [];
    $('.scheduling-queue .queue-label').each(function() {
        $i = $(this).parent();
        
        $schedRow = {};
        
        $schedRow['id'] = $i.find('.queue-label').attr('id').split('-')[2];
        $schedRow['label'] = $i.find('.queue-label').text();
        $schedRow['level'] = $i.find('.queue-level').attr('id');
        $schedRow['unit'] = $i.find('.queue-unit').attr('id');
        $schedRow['lesson'] = $i.find('.queue-lesson').attr('id');
        
        $schedQueueArray.push($schedRow);
    });
    
    return $schedQueueArray;
}

// FUNCTION Sort Schedule Queue Array (a = $schedQueueArray, b = parameter to sort {id,label,level,unit,lesson})

function compareLabel(a,b) {
    if (a.label < b.label) {
        return -1;
    }
    if (a.label > b.label) {
        return 1;
    }
    return 0;
}
function compareLesson(a,b) {
    if (a.lesson < b.lesson) {
        return -1;
    }
    if (a.lesson > b.lesson) {
        return 1;
    }
    return 0;
}
function compareLevel(a,b) {
    return a.level - b.level;
}
function compareUnit(a,b) {
    return a.unit - b.unit;
}
function compareId(a,b) {
    return a.id - b.id;
}
function sortSchedQueueArray(b) {
    
    a = genSchedQueueArray();
    $l = a.length;
    
    if( b == 'id' ) {
        a.sort(compareId);
    } else if ( b == 'label' ) {
        a.sort(compareLabel);
    } else if ( b == 'level' ) {
        a.sort(compareLevel);
    } else if ( b == 'unit' ) {
        a.sort(compareUnit);
    } else if ( b == 'lesson' ) {
        a.sort(compareLesson);
    } else {
        a.sort(compareId);
    }
    
    return a;
}

// FUNCTION Wewrite Scheduling Queue Table (b = parameter to sort {id,label,level,unit,lesson})

function rewriteSchedQueue(b) {
    a = sortSchedQueueArray(b);
    l = a.length;
    $('.scheduled-item').each(function() {
        $id = $(this).find('.queue-label').attr('id').split('-')[2];
        removeFromQueue($id,'x');
    });    
    for( var i = 0; i < l; i++ ) {        
        addToQueue( a[i]['id'] );
    }
    if( typeof $uid != 'undefined' ) {
        writeStoredActivities($uid,formatAct());
    }
}

// Reload Page Every Hour (for iCulture Hash)

window.onload = function() {
    
    d = 3600000 - ( Date.now() % 3600000 );
    window.setTimeout(function(){
        location.reload(true);
    }, d);    
};

// Enable Mobile Swipe for Modal Container Left and Right Click

document.addEventListener('touchstart', handleTouchStart, false);        
document.addEventListener('touchmove', handleTouchMove, false);

var xDown = null;                                                        
var yDown = null;                                                        

function handleTouchStart(evt) {                                         
    xDown = evt.touches[0].clientX;                                      
    yDown = evt.touches[0].clientY;                                      
};                                                

function handleTouchMove(evt) {
    if ( ! xDown || ! yDown ) {
        return;
    }

    var xUp = evt.touches[0].clientX;                                    
    var yUp = evt.touches[0].clientY;

    var xDiff = xDown - xUp;
    var yDiff = yDown - yUp;

    if ( Math.abs( xDiff ) > Math.abs( yDiff ) ) {/*most significant*/
        if ( xDiff > 0 ) {
            $('.modalContainer .right-arrow').click();
        } else {
            $('.modalContainer .left-arrow').click();
        }                       
    }
    xDown = null;
    yDown = null;                                             
};

// FUNCTION Check if user is logged in to Bookshelf yet

function bookshelfSessionGrab() {
    $this = $('.resource_item:first .resource_modal_info');
    $BSURL = $this.find('.resource-meta-data.url').attr('id').split('#')[0];
    $BSjsURL = $BSURL + 'OEBPS/Data/' + $this.find('.resource-meta-data.page').attr('id') + '.js';

    $BSstatusCheck = $.ajax({
        url: $BSjsURL,
        dataType: 'script'
    });

    return $BSstatusCheck;
}

function bookshelfSessionCheck($a) {
    
    if( $a['statusText'] ) {
        return true;
        console.log('true');
    } else {
        return false;
        console.log('false');
    }
    
}


// ------------- Bookshelf Scripts ---------------- //

//jQuery UI 1.10.3

/*! jQuery UI - v1.10.3 - 2013-05-29
* http://jqueryui.com
* Includes: jquery.ui.core.js, jquery.ui.widget.js, jquery.ui.mouse.js, jquery.ui.position.js, jquery.ui.draggable.js, jquery.ui.resizable.js, jquery.ui.button.js, jquery.ui.dialog.js
* Copyright 2013 jQuery Foundation and other contributors Licensed MIT */

(function(e,t){function i(t,i){var a,n,r,o=t.nodeName.toLowerCase();return"area"===o?(a=t.parentNode,n=a.name,t.href&&n&&"map"===a.nodeName.toLowerCase()?(r=e("img[usemap=#"+n+"]")[0],!!r&&s(r)):!1):(/input|select|textarea|button|object/.test(o)?!t.disabled:"a"===o?t.href||i:i)&&s(t)}function s(t){return e.expr.filters.visible(t)&&!e(t).parents().addBack().filter(function(){return"hidden"===e.css(this,"visibility")}).length}var a=0,n=/^ui-id-\d+$/;e.ui=e.ui||{},e.extend(e.ui,{version:"1.10.3",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),e.fn.extend({focus:function(t){return function(i,s){return"number"==typeof i?this.each(function(){var t=this;setTimeout(function(){e(t).focus(),s&&s.call(t)},i)}):t.apply(this,arguments)}}(e.fn.focus),scrollParent:function(){var t;return t=e.ui.ie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?this.parents().filter(function(){return/(relative|absolute|fixed)/.test(e.css(this,"position"))&&/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0):this.parents().filter(function(){return/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0),/fixed/.test(this.css("position"))||!t.length?e(document):t},zIndex:function(i){if(i!==t)return this.css("zIndex",i);if(this.length)for(var s,a,n=e(this[0]);n.length&&n[0]!==document;){if(s=n.css("position"),("absolute"===s||"relative"===s||"fixed"===s)&&(a=parseInt(n.css("zIndex"),10),!isNaN(a)&&0!==a))return a;n=n.parent()}return 0},uniqueId:function(){return this.each(function(){this.id||(this.id="ui-id-"+ ++a)})},removeUniqueId:function(){return this.each(function(){n.test(this.id)&&e(this).removeAttr("id")})}}),e.extend(e.expr[":"],{data:e.expr.createPseudo?e.expr.createPseudo(function(t){return function(i){return!!e.data(i,t)}}):function(t,i,s){return!!e.data(t,s[3])},focusable:function(t){return i(t,!isNaN(e.attr(t,"tabindex")))},tabbable:function(t){var s=e.attr(t,"tabindex"),a=isNaN(s);return(a||s>=0)&&i(t,!a)}}),e("<a>").outerWidth(1).jquery||e.each(["Width","Height"],function(i,s){function a(t,i,s,a){return e.each(n,function(){i-=parseFloat(e.css(t,"padding"+this))||0,s&&(i-=parseFloat(e.css(t,"border"+this+"Width"))||0),a&&(i-=parseFloat(e.css(t,"margin"+this))||0)}),i}var n="Width"===s?["Left","Right"]:["Top","Bottom"],r=s.toLowerCase(),o={innerWidth:e.fn.innerWidth,innerHeight:e.fn.innerHeight,outerWidth:e.fn.outerWidth,outerHeight:e.fn.outerHeight};e.fn["inner"+s]=function(i){return i===t?o["inner"+s].call(this):this.each(function(){e(this).css(r,a(this,i)+"px")})},e.fn["outer"+s]=function(t,i){return"number"!=typeof t?o["outer"+s].call(this,t):this.each(function(){e(this).css(r,a(this,t,!0,i)+"px")})}}),e.fn.addBack||(e.fn.addBack=function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}),e("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(e.fn.removeData=function(t){return function(i){return arguments.length?t.call(this,e.camelCase(i)):t.call(this)}}(e.fn.removeData)),e.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()),e.support.selectstart="onselectstart"in document.createElement("div"),e.fn.extend({disableSelection:function(){return this.bind((e.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(e){e.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),e.extend(e.ui,{plugin:{add:function(t,i,s){var a,n=e.ui[t].prototype;for(a in s)n.plugins[a]=n.plugins[a]||[],n.plugins[a].push([i,s[a]])},call:function(e,t,i){var s,a=e.plugins[t];if(a&&e.element[0].parentNode&&11!==e.element[0].parentNode.nodeType)for(s=0;a.length>s;s++)e.options[a[s][0]]&&a[s][1].apply(e.element,i)}},hasScroll:function(t,i){if("hidden"===e(t).css("overflow"))return!1;var s=i&&"left"===i?"scrollLeft":"scrollTop",a=!1;return t[s]>0?!0:(t[s]=1,a=t[s]>0,t[s]=0,a)}})})(jQuery);(function(e,t){var i=0,s=Array.prototype.slice,n=e.cleanData;e.cleanData=function(t){for(var i,s=0;null!=(i=t[s]);s++)try{e(i).triggerHandler("remove")}catch(a){}n(t)},e.widget=function(i,s,n){var a,r,o,h,l={},u=i.split(".")[0];i=i.split(".")[1],a=u+"-"+i,n||(n=s,s=e.Widget),e.expr[":"][a.toLowerCase()]=function(t){return!!e.data(t,a)},e[u]=e[u]||{},r=e[u][i],o=e[u][i]=function(e,i){return this._createWidget?(arguments.length&&this._createWidget(e,i),t):new o(e,i)},e.extend(o,r,{version:n.version,_proto:e.extend({},n),_childConstructors:[]}),h=new s,h.options=e.widget.extend({},h.options),e.each(n,function(i,n){return e.isFunction(n)?(l[i]=function(){var e=function(){return s.prototype[i].apply(this,arguments)},t=function(e){return s.prototype[i].apply(this,e)};return function(){var i,s=this._super,a=this._superApply;return this._super=e,this._superApply=t,i=n.apply(this,arguments),this._super=s,this._superApply=a,i}}(),t):(l[i]=n,t)}),o.prototype=e.widget.extend(h,{widgetEventPrefix:r?h.widgetEventPrefix:i},l,{constructor:o,namespace:u,widgetName:i,widgetFullName:a}),r?(e.each(r._childConstructors,function(t,i){var s=i.prototype;e.widget(s.namespace+"."+s.widgetName,o,i._proto)}),delete r._childConstructors):s._childConstructors.push(o),e.widget.bridge(i,o)},e.widget.extend=function(i){for(var n,a,r=s.call(arguments,1),o=0,h=r.length;h>o;o++)for(n in r[o])a=r[o][n],r[o].hasOwnProperty(n)&&a!==t&&(i[n]=e.isPlainObject(a)?e.isPlainObject(i[n])?e.widget.extend({},i[n],a):e.widget.extend({},a):a);return i},e.widget.bridge=function(i,n){var a=n.prototype.widgetFullName||i;e.fn[i]=function(r){var o="string"==typeof r,h=s.call(arguments,1),l=this;return r=!o&&h.length?e.widget.extend.apply(null,[r].concat(h)):r,o?this.each(function(){var s,n=e.data(this,a);return n?e.isFunction(n[r])&&"_"!==r.charAt(0)?(s=n[r].apply(n,h),s!==n&&s!==t?(l=s&&s.jquery?l.pushStack(s.get()):s,!1):t):e.error("no such method '"+r+"' for "+i+" widget instance"):e.error("cannot call methods on "+i+" prior to initialization; "+"attempted to call method '"+r+"'")}):this.each(function(){var t=e.data(this,a);t?t.option(r||{})._init():e.data(this,a,new n(r,this))}),l}},e.Widget=function(){},e.Widget._childConstructors=[],e.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(t,s){s=e(s||this.defaultElement||this)[0],this.element=e(s),this.uuid=i++,this.eventNamespace="."+this.widgetName+this.uuid,this.options=e.widget.extend({},this.options,this._getCreateOptions(),t),this.bindings=e(),this.hoverable=e(),this.focusable=e(),s!==this&&(e.data(s,this.widgetFullName,this),this._on(!0,this.element,{remove:function(e){e.target===s&&this.destroy()}}),this.document=e(s.style?s.ownerDocument:s.document||s),this.window=e(this.document[0].defaultView||this.document[0].parentWindow)),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:e.noop,_getCreateEventData:e.noop,_create:e.noop,_init:e.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetName).removeData(this.widgetFullName).removeData(e.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled "+"ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:e.noop,widget:function(){return this.element},option:function(i,s){var n,a,r,o=i;if(0===arguments.length)return e.widget.extend({},this.options);if("string"==typeof i)if(o={},n=i.split("."),i=n.shift(),n.length){for(a=o[i]=e.widget.extend({},this.options[i]),r=0;n.length-1>r;r++)a[n[r]]=a[n[r]]||{},a=a[n[r]];if(i=n.pop(),s===t)return a[i]===t?null:a[i];a[i]=s}else{if(s===t)return this.options[i]===t?null:this.options[i];o[i]=s}return this._setOptions(o),this},_setOptions:function(e){var t;for(t in e)this._setOption(t,e[t]);return this},_setOption:function(e,t){return this.options[e]=t,"disabled"===e&&(this.widget().toggleClass(this.widgetFullName+"-disabled ui-state-disabled",!!t).attr("aria-disabled",t),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")),this},enable:function(){return this._setOption("disabled",!1)},disable:function(){return this._setOption("disabled",!0)},_on:function(i,s,n){var a,r=this;"boolean"!=typeof i&&(n=s,s=i,i=!1),n?(s=a=e(s),this.bindings=this.bindings.add(s)):(n=s,s=this.element,a=this.widget()),e.each(n,function(n,o){function h(){return i||r.options.disabled!==!0&&!e(this).hasClass("ui-state-disabled")?("string"==typeof o?r[o]:o).apply(r,arguments):t}"string"!=typeof o&&(h.guid=o.guid=o.guid||h.guid||e.guid++);var l=n.match(/^(\w+)\s*(.*)$/),u=l[1]+r.eventNamespace,c=l[2];c?a.delegate(c,u,h):s.bind(u,h)})},_off:function(e,t){t=(t||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,e.unbind(t).undelegate(t)},_delay:function(e,t){function i(){return("string"==typeof e?s[e]:e).apply(s,arguments)}var s=this;return setTimeout(i,t||0)},_hoverable:function(t){this.hoverable=this.hoverable.add(t),this._on(t,{mouseenter:function(t){e(t.currentTarget).addClass("ui-state-hover")},mouseleave:function(t){e(t.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(t){this.focusable=this.focusable.add(t),this._on(t,{focusin:function(t){e(t.currentTarget).addClass("ui-state-focus")},focusout:function(t){e(t.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(t,i,s){var n,a,r=this.options[t];if(s=s||{},i=e.Event(i),i.type=(t===this.widgetEventPrefix?t:this.widgetEventPrefix+t).toLowerCase(),i.target=this.element[0],a=i.originalEvent)for(n in a)n in i||(i[n]=a[n]);return this.element.trigger(i,s),!(e.isFunction(r)&&r.apply(this.element[0],[i].concat(s))===!1||i.isDefaultPrevented())}},e.each({show:"fadeIn",hide:"fadeOut"},function(t,i){e.Widget.prototype["_"+t]=function(s,n,a){"string"==typeof n&&(n={effect:n});var r,o=n?n===!0||"number"==typeof n?i:n.effect||i:t;n=n||{},"number"==typeof n&&(n={duration:n}),r=!e.isEmptyObject(n),n.complete=a,n.delay&&s.delay(n.delay),r&&e.effects&&e.effects.effect[o]?s[t](n):o!==t&&s[o]?s[o](n.duration,n.easing,a):s.queue(function(i){e(this)[t](),a&&a.call(s[0]),i()})}})})(jQuery);(function(e){var t=!1;e(document).mouseup(function(){t=!1}),e.widget("ui.mouse",{version:"1.10.3",options:{cancel:"input,textarea,button,select,option",distance:1,delay:0},_mouseInit:function(){var t=this;this.element.bind("mousedown."+this.widgetName,function(e){return t._mouseDown(e)}).bind("click."+this.widgetName,function(i){return!0===e.data(i.target,t.widgetName+".preventClickEvent")?(e.removeData(i.target,t.widgetName+".preventClickEvent"),i.stopImmediatePropagation(),!1):undefined}),this.started=!1},_mouseDestroy:function(){this.element.unbind("."+this.widgetName),this._mouseMoveDelegate&&e(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate)},_mouseDown:function(i){if(!t){this._mouseStarted&&this._mouseUp(i),this._mouseDownEvent=i;var s=this,n=1===i.which,a="string"==typeof this.options.cancel&&i.target.nodeName?e(i.target).closest(this.options.cancel).length:!1;return n&&!a&&this._mouseCapture(i)?(this.mouseDelayMet=!this.options.delay,this.mouseDelayMet||(this._mouseDelayTimer=setTimeout(function(){s.mouseDelayMet=!0},this.options.delay)),this._mouseDistanceMet(i)&&this._mouseDelayMet(i)&&(this._mouseStarted=this._mouseStart(i)!==!1,!this._mouseStarted)?(i.preventDefault(),!0):(!0===e.data(i.target,this.widgetName+".preventClickEvent")&&e.removeData(i.target,this.widgetName+".preventClickEvent"),this._mouseMoveDelegate=function(e){return s._mouseMove(e)},this._mouseUpDelegate=function(e){return s._mouseUp(e)},e(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate),i.preventDefault(),t=!0,!0)):!0}},_mouseMove:function(t){return e.ui.ie&&(!document.documentMode||9>document.documentMode)&&!t.button?this._mouseUp(t):this._mouseStarted?(this._mouseDrag(t),t.preventDefault()):(this._mouseDistanceMet(t)&&this._mouseDelayMet(t)&&(this._mouseStarted=this._mouseStart(this._mouseDownEvent,t)!==!1,this._mouseStarted?this._mouseDrag(t):this._mouseUp(t)),!this._mouseStarted)},_mouseUp:function(t){return e(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate),this._mouseStarted&&(this._mouseStarted=!1,t.target===this._mouseDownEvent.target&&e.data(t.target,this.widgetName+".preventClickEvent",!0),this._mouseStop(t)),!1},_mouseDistanceMet:function(e){return Math.max(Math.abs(this._mouseDownEvent.pageX-e.pageX),Math.abs(this._mouseDownEvent.pageY-e.pageY))>=this.options.distance},_mouseDelayMet:function(){return this.mouseDelayMet},_mouseStart:function(){},_mouseDrag:function(){},_mouseStop:function(){},_mouseCapture:function(){return!0}})})(jQuery);(function(t,e){function i(t,e,i){return[parseFloat(t[0])*(p.test(t[0])?e/100:1),parseFloat(t[1])*(p.test(t[1])?i/100:1)]}function s(e,i){return parseInt(t.css(e,i),10)||0}function n(e){var i=e[0];return 9===i.nodeType?{width:e.width(),height:e.height(),offset:{top:0,left:0}}:t.isWindow(i)?{width:e.width(),height:e.height(),offset:{top:e.scrollTop(),left:e.scrollLeft()}}:i.preventDefault?{width:0,height:0,offset:{top:i.pageY,left:i.pageX}}:{width:e.outerWidth(),height:e.outerHeight(),offset:e.offset()}}t.ui=t.ui||{};var a,o=Math.max,r=Math.abs,h=Math.round,l=/left|center|right/,c=/top|center|bottom/,u=/[\+\-]\d+(\.[\d]+)?%?/,d=/^\w+/,p=/%$/,f=t.fn.position;t.position={scrollbarWidth:function(){if(a!==e)return a;var i,s,n=t("<div style='display:block;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>"),o=n.children()[0];return t("body").append(n),i=o.offsetWidth,n.css("overflow","scroll"),s=o.offsetWidth,i===s&&(s=n[0].clientWidth),n.remove(),a=i-s},getScrollInfo:function(e){var i=e.isWindow?"":e.element.css("overflow-x"),s=e.isWindow?"":e.element.css("overflow-y"),n="scroll"===i||"auto"===i&&e.width<e.element[0].scrollWidth,a="scroll"===s||"auto"===s&&e.height<e.element[0].scrollHeight;return{width:a?t.position.scrollbarWidth():0,height:n?t.position.scrollbarWidth():0}},getWithinInfo:function(e){var i=t(e||window),s=t.isWindow(i[0]);return{element:i,isWindow:s,offset:i.offset()||{left:0,top:0},scrollLeft:i.scrollLeft(),scrollTop:i.scrollTop(),width:s?i.width():i.outerWidth(),height:s?i.height():i.outerHeight()}}},t.fn.position=function(e){if(!e||!e.of)return f.apply(this,arguments);e=t.extend({},e);var a,p,m,g,v,b,_=t(e.of),y=t.position.getWithinInfo(e.within),w=t.position.getScrollInfo(y),x=(e.collision||"flip").split(" "),k={};return b=n(_),_[0].preventDefault&&(e.at="left top"),p=b.width,m=b.height,g=b.offset,v=t.extend({},g),t.each(["my","at"],function(){var t,i,s=(e[this]||"").split(" ");1===s.length&&(s=l.test(s[0])?s.concat(["center"]):c.test(s[0])?["center"].concat(s):["center","center"]),s[0]=l.test(s[0])?s[0]:"center",s[1]=c.test(s[1])?s[1]:"center",t=u.exec(s[0]),i=u.exec(s[1]),k[this]=[t?t[0]:0,i?i[0]:0],e[this]=[d.exec(s[0])[0],d.exec(s[1])[0]]}),1===x.length&&(x[1]=x[0]),"right"===e.at[0]?v.left+=p:"center"===e.at[0]&&(v.left+=p/2),"bottom"===e.at[1]?v.top+=m:"center"===e.at[1]&&(v.top+=m/2),a=i(k.at,p,m),v.left+=a[0],v.top+=a[1],this.each(function(){var n,l,c=t(this),u=c.outerWidth(),d=c.outerHeight(),f=s(this,"marginLeft"),b=s(this,"marginTop"),D=u+f+s(this,"marginRight")+w.width,T=d+b+s(this,"marginBottom")+w.height,C=t.extend({},v),M=i(k.my,c.outerWidth(),c.outerHeight());"right"===e.my[0]?C.left-=u:"center"===e.my[0]&&(C.left-=u/2),"bottom"===e.my[1]?C.top-=d:"center"===e.my[1]&&(C.top-=d/2),C.left+=M[0],C.top+=M[1],t.support.offsetFractions||(C.left=h(C.left),C.top=h(C.top)),n={marginLeft:f,marginTop:b},t.each(["left","top"],function(i,s){t.ui.position[x[i]]&&t.ui.position[x[i]][s](C,{targetWidth:p,targetHeight:m,elemWidth:u,elemHeight:d,collisionPosition:n,collisionWidth:D,collisionHeight:T,offset:[a[0]+M[0],a[1]+M[1]],my:e.my,at:e.at,within:y,elem:c})}),e.using&&(l=function(t){var i=g.left-C.left,s=i+p-u,n=g.top-C.top,a=n+m-d,h={target:{element:_,left:g.left,top:g.top,width:p,height:m},element:{element:c,left:C.left,top:C.top,width:u,height:d},horizontal:0>s?"left":i>0?"right":"center",vertical:0>a?"top":n>0?"bottom":"middle"};u>p&&p>r(i+s)&&(h.horizontal="center"),d>m&&m>r(n+a)&&(h.vertical="middle"),h.important=o(r(i),r(s))>o(r(n),r(a))?"horizontal":"vertical",e.using.call(this,t,h)}),c.offset(t.extend(C,{using:l}))})},t.ui.position={fit:{left:function(t,e){var i,s=e.within,n=s.isWindow?s.scrollLeft:s.offset.left,a=s.width,r=t.left-e.collisionPosition.marginLeft,h=n-r,l=r+e.collisionWidth-a-n;e.collisionWidth>a?h>0&&0>=l?(i=t.left+h+e.collisionWidth-a-n,t.left+=h-i):t.left=l>0&&0>=h?n:h>l?n+a-e.collisionWidth:n:h>0?t.left+=h:l>0?t.left-=l:t.left=o(t.left-r,t.left)},top:function(t,e){var i,s=e.within,n=s.isWindow?s.scrollTop:s.offset.top,a=e.within.height,r=t.top-e.collisionPosition.marginTop,h=n-r,l=r+e.collisionHeight-a-n;e.collisionHeight>a?h>0&&0>=l?(i=t.top+h+e.collisionHeight-a-n,t.top+=h-i):t.top=l>0&&0>=h?n:h>l?n+a-e.collisionHeight:n:h>0?t.top+=h:l>0?t.top-=l:t.top=o(t.top-r,t.top)}},flip:{left:function(t,e){var i,s,n=e.within,a=n.offset.left+n.scrollLeft,o=n.width,h=n.isWindow?n.scrollLeft:n.offset.left,l=t.left-e.collisionPosition.marginLeft,c=l-h,u=l+e.collisionWidth-o-h,d="left"===e.my[0]?-e.elemWidth:"right"===e.my[0]?e.elemWidth:0,p="left"===e.at[0]?e.targetWidth:"right"===e.at[0]?-e.targetWidth:0,f=-2*e.offset[0];0>c?(i=t.left+d+p+f+e.collisionWidth-o-a,(0>i||r(c)>i)&&(t.left+=d+p+f)):u>0&&(s=t.left-e.collisionPosition.marginLeft+d+p+f-h,(s>0||u>r(s))&&(t.left+=d+p+f))},top:function(t,e){var i,s,n=e.within,a=n.offset.top+n.scrollTop,o=n.height,h=n.isWindow?n.scrollTop:n.offset.top,l=t.top-e.collisionPosition.marginTop,c=l-h,u=l+e.collisionHeight-o-h,d="top"===e.my[1],p=d?-e.elemHeight:"bottom"===e.my[1]?e.elemHeight:0,f="top"===e.at[1]?e.targetHeight:"bottom"===e.at[1]?-e.targetHeight:0,m=-2*e.offset[1];0>c?(s=t.top+p+f+m+e.collisionHeight-o-a,t.top+p+f+m>c&&(0>s||r(c)>s)&&(t.top+=p+f+m)):u>0&&(i=t.top-e.collisionPosition.marginTop+p+f+m-h,t.top+p+f+m>u&&(i>0||u>r(i))&&(t.top+=p+f+m))}},flipfit:{left:function(){t.ui.position.flip.left.apply(this,arguments),t.ui.position.fit.left.apply(this,arguments)},top:function(){t.ui.position.flip.top.apply(this,arguments),t.ui.position.fit.top.apply(this,arguments)}}},function(){var e,i,s,n,a,o=document.getElementsByTagName("body")[0],r=document.createElement("div");e=document.createElement(o?"div":"body"),s={visibility:"hidden",width:0,height:0,border:0,margin:0,background:"none"},o&&t.extend(s,{position:"absolute",left:"-1000px",top:"-1000px"});for(a in s)e.style[a]=s[a];e.appendChild(r),i=o||document.documentElement,i.insertBefore(e,i.firstChild),r.style.cssText="position: absolute; left: 10.7432222px;",n=t(r).offset().left,t.support.offsetFractions=n>10&&11>n,e.innerHTML="",i.removeChild(e)}()})(jQuery);(function(e){e.widget("ui.draggable",e.ui.mouse,{version:"1.10.3",widgetEventPrefix:"drag",options:{addClasses:!0,appendTo:"parent",axis:!1,connectToSortable:!1,containment:!1,cursor:"auto",cursorAt:!1,grid:!1,handle:!1,helper:"original",iframeFix:!1,opacity:!1,refreshPositions:!1,revert:!1,revertDuration:500,scope:"default",scroll:!0,scrollSensitivity:20,scrollSpeed:20,snap:!1,snapMode:"both",snapTolerance:20,stack:!1,zIndex:!1,drag:null,start:null,stop:null},_create:function(){"original"!==this.options.helper||/^(?:r|a|f)/.test(this.element.css("position"))||(this.element[0].style.position="relative"),this.options.addClasses&&this.element.addClass("ui-draggable"),this.options.disabled&&this.element.addClass("ui-draggable-disabled"),this._mouseInit()},_destroy:function(){this.element.removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled"),this._mouseDestroy()},_mouseCapture:function(t){var i=this.options;return this.helper||i.disabled||e(t.target).closest(".ui-resizable-handle").length>0?!1:(this.handle=this._getHandle(t),this.handle?(e(i.iframeFix===!0?"iframe":i.iframeFix).each(function(){e("<div class='ui-draggable-iframeFix' style='background: #fff;'></div>").css({width:this.offsetWidth+"px",height:this.offsetHeight+"px",position:"absolute",opacity:"0.001",zIndex:1e3}).css(e(this).offset()).appendTo("body")}),!0):!1)},_mouseStart:function(t){var i=this.options;return this.helper=this._createHelper(t),this.helper.addClass("ui-draggable-dragging"),this._cacheHelperProportions(),e.ui.ddmanager&&(e.ui.ddmanager.current=this),this._cacheMargins(),this.cssPosition=this.helper.css("position"),this.scrollParent=this.helper.scrollParent(),this.offsetParent=this.helper.offsetParent(),this.offsetParentCssPosition=this.offsetParent.css("position"),this.offset=this.positionAbs=this.element.offset(),this.offset={top:this.offset.top-this.margins.top,left:this.offset.left-this.margins.left},this.offset.scroll=!1,e.extend(this.offset,{click:{left:t.pageX-this.offset.left,top:t.pageY-this.offset.top},parent:this._getParentOffset(),relative:this._getRelativeOffset()}),this.originalPosition=this.position=this._generatePosition(t),this.originalPageX=t.pageX,this.originalPageY=t.pageY,i.cursorAt&&this._adjustOffsetFromHelper(i.cursorAt),this._setContainment(),this._trigger("start",t)===!1?(this._clear(),!1):(this._cacheHelperProportions(),e.ui.ddmanager&&!i.dropBehaviour&&e.ui.ddmanager.prepareOffsets(this,t),this._mouseDrag(t,!0),e.ui.ddmanager&&e.ui.ddmanager.dragStart(this,t),!0)},_mouseDrag:function(t,i){if("fixed"===this.offsetParentCssPosition&&(this.offset.parent=this._getParentOffset()),this.position=this._generatePosition(t),this.positionAbs=this._convertPositionTo("absolute"),!i){var s=this._uiHash();if(this._trigger("drag",t,s)===!1)return this._mouseUp({}),!1;this.position=s.position}return this.options.axis&&"y"===this.options.axis||(this.helper[0].style.left=this.position.left+"px"),this.options.axis&&"x"===this.options.axis||(this.helper[0].style.top=this.position.top+"px"),e.ui.ddmanager&&e.ui.ddmanager.drag(this,t),!1},_mouseStop:function(t){var i=this,s=!1;return e.ui.ddmanager&&!this.options.dropBehaviour&&(s=e.ui.ddmanager.drop(this,t)),this.dropped&&(s=this.dropped,this.dropped=!1),"original"!==this.options.helper||e.contains(this.element[0].ownerDocument,this.element[0])?("invalid"===this.options.revert&&!s||"valid"===this.options.revert&&s||this.options.revert===!0||e.isFunction(this.options.revert)&&this.options.revert.call(this.element,s)?e(this.helper).animate(this.originalPosition,parseInt(this.options.revertDuration,10),function(){i._trigger("stop",t)!==!1&&i._clear()}):this._trigger("stop",t)!==!1&&this._clear(),!1):!1},_mouseUp:function(t){return e("div.ui-draggable-iframeFix").each(function(){this.parentNode.removeChild(this)}),e.ui.ddmanager&&e.ui.ddmanager.dragStop(this,t),e.ui.mouse.prototype._mouseUp.call(this,t)},cancel:function(){return this.helper.is(".ui-draggable-dragging")?this._mouseUp({}):this._clear(),this},_getHandle:function(t){return this.options.handle?!!e(t.target).closest(this.element.find(this.options.handle)).length:!0},_createHelper:function(t){var i=this.options,s=e.isFunction(i.helper)?e(i.helper.apply(this.element[0],[t])):"clone"===i.helper?this.element.clone().removeAttr("id"):this.element;return s.parents("body").length||s.appendTo("parent"===i.appendTo?this.element[0].parentNode:i.appendTo),s[0]===this.element[0]||/(fixed|absolute)/.test(s.css("position"))||s.css("position","absolute"),s},_adjustOffsetFromHelper:function(t){"string"==typeof t&&(t=t.split(" ")),e.isArray(t)&&(t={left:+t[0],top:+t[1]||0}),"left"in t&&(this.offset.click.left=t.left+this.margins.left),"right"in t&&(this.offset.click.left=this.helperProportions.width-t.right+this.margins.left),"top"in t&&(this.offset.click.top=t.top+this.margins.top),"bottom"in t&&(this.offset.click.top=this.helperProportions.height-t.bottom+this.margins.top)},_getParentOffset:function(){var t=this.offsetParent.offset();return"absolute"===this.cssPosition&&this.scrollParent[0]!==document&&e.contains(this.scrollParent[0],this.offsetParent[0])&&(t.left+=this.scrollParent.scrollLeft(),t.top+=this.scrollParent.scrollTop()),(this.offsetParent[0]===document.body||this.offsetParent[0].tagName&&"html"===this.offsetParent[0].tagName.toLowerCase()&&e.ui.ie)&&(t={top:0,left:0}),{top:t.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:t.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)}},_getRelativeOffset:function(){if("relative"===this.cssPosition){var e=this.element.position();return{top:e.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),left:e.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()}}return{top:0,left:0}},_cacheMargins:function(){this.margins={left:parseInt(this.element.css("marginLeft"),10)||0,top:parseInt(this.element.css("marginTop"),10)||0,right:parseInt(this.element.css("marginRight"),10)||0,bottom:parseInt(this.element.css("marginBottom"),10)||0}},_cacheHelperProportions:function(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()}},_setContainment:function(){var t,i,s,n=this.options;return n.containment?"window"===n.containment?(this.containment=[e(window).scrollLeft()-this.offset.relative.left-this.offset.parent.left,e(window).scrollTop()-this.offset.relative.top-this.offset.parent.top,e(window).scrollLeft()+e(window).width()-this.helperProportions.width-this.margins.left,e(window).scrollTop()+(e(window).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top],undefined):"document"===n.containment?(this.containment=[0,0,e(document).width()-this.helperProportions.width-this.margins.left,(e(document).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top],undefined):n.containment.constructor===Array?(this.containment=n.containment,undefined):("parent"===n.containment&&(n.containment=this.helper[0].parentNode),i=e(n.containment),s=i[0],s&&(t="hidden"!==i.css("overflow"),this.containment=[(parseInt(i.css("borderLeftWidth"),10)||0)+(parseInt(i.css("paddingLeft"),10)||0),(parseInt(i.css("borderTopWidth"),10)||0)+(parseInt(i.css("paddingTop"),10)||0),(t?Math.max(s.scrollWidth,s.offsetWidth):s.offsetWidth)-(parseInt(i.css("borderRightWidth"),10)||0)-(parseInt(i.css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left-this.margins.right,(t?Math.max(s.scrollHeight,s.offsetHeight):s.offsetHeight)-(parseInt(i.css("borderBottomWidth"),10)||0)-(parseInt(i.css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top-this.margins.bottom],this.relative_container=i),undefined):(this.containment=null,undefined)},_convertPositionTo:function(t,i){i||(i=this.position);var s="absolute"===t?1:-1,n="absolute"!==this.cssPosition||this.scrollParent[0]!==document&&e.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent;return this.offset.scroll||(this.offset.scroll={top:n.scrollTop(),left:n.scrollLeft()}),{top:i.top+this.offset.relative.top*s+this.offset.parent.top*s-("fixed"===this.cssPosition?-this.scrollParent.scrollTop():this.offset.scroll.top)*s,left:i.left+this.offset.relative.left*s+this.offset.parent.left*s-("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():this.offset.scroll.left)*s}},_generatePosition:function(t){var i,s,n,a,o=this.options,r="absolute"!==this.cssPosition||this.scrollParent[0]!==document&&e.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent,h=t.pageX,l=t.pageY;return this.offset.scroll||(this.offset.scroll={top:r.scrollTop(),left:r.scrollLeft()}),this.originalPosition&&(this.containment&&(this.relative_container?(s=this.relative_container.offset(),i=[this.containment[0]+s.left,this.containment[1]+s.top,this.containment[2]+s.left,this.containment[3]+s.top]):i=this.containment,t.pageX-this.offset.click.left<i[0]&&(h=i[0]+this.offset.click.left),t.pageY-this.offset.click.top<i[1]&&(l=i[1]+this.offset.click.top),t.pageX-this.offset.click.left>i[2]&&(h=i[2]+this.offset.click.left),t.pageY-this.offset.click.top>i[3]&&(l=i[3]+this.offset.click.top)),o.grid&&(n=o.grid[1]?this.originalPageY+Math.round((l-this.originalPageY)/o.grid[1])*o.grid[1]:this.originalPageY,l=i?n-this.offset.click.top>=i[1]||n-this.offset.click.top>i[3]?n:n-this.offset.click.top>=i[1]?n-o.grid[1]:n+o.grid[1]:n,a=o.grid[0]?this.originalPageX+Math.round((h-this.originalPageX)/o.grid[0])*o.grid[0]:this.originalPageX,h=i?a-this.offset.click.left>=i[0]||a-this.offset.click.left>i[2]?a:a-this.offset.click.left>=i[0]?a-o.grid[0]:a+o.grid[0]:a)),{top:l-this.offset.click.top-this.offset.relative.top-this.offset.parent.top+("fixed"===this.cssPosition?-this.scrollParent.scrollTop():this.offset.scroll.top),left:h-this.offset.click.left-this.offset.relative.left-this.offset.parent.left+("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():this.offset.scroll.left)}},_clear:function(){this.helper.removeClass("ui-draggable-dragging"),this.helper[0]===this.element[0]||this.cancelHelperRemoval||this.helper.remove(),this.helper=null,this.cancelHelperRemoval=!1},_trigger:function(t,i,s){return s=s||this._uiHash(),e.ui.plugin.call(this,t,[i,s]),"drag"===t&&(this.positionAbs=this._convertPositionTo("absolute")),e.Widget.prototype._trigger.call(this,t,i,s)},plugins:{},_uiHash:function(){return{helper:this.helper,position:this.position,originalPosition:this.originalPosition,offset:this.positionAbs}}}),e.ui.plugin.add("draggable","connectToSortable",{start:function(t,i){var s=e(this).data("ui-draggable"),n=s.options,a=e.extend({},i,{item:s.element});s.sortables=[],e(n.connectToSortable).each(function(){var i=e.data(this,"ui-sortable");i&&!i.options.disabled&&(s.sortables.push({instance:i,shouldRevert:i.options.revert}),i.refreshPositions(),i._trigger("activate",t,a))})},stop:function(t,i){var s=e(this).data("ui-draggable"),n=e.extend({},i,{item:s.element});e.each(s.sortables,function(){this.instance.isOver?(this.instance.isOver=0,s.cancelHelperRemoval=!0,this.instance.cancelHelperRemoval=!1,this.shouldRevert&&(this.instance.options.revert=this.shouldRevert),this.instance._mouseStop(t),this.instance.options.helper=this.instance.options._helper,"original"===s.options.helper&&this.instance.currentItem.css({top:"auto",left:"auto"})):(this.instance.cancelHelperRemoval=!1,this.instance._trigger("deactivate",t,n))})},drag:function(t,i){var s=e(this).data("ui-draggable"),n=this;e.each(s.sortables,function(){var a=!1,o=this;this.instance.positionAbs=s.positionAbs,this.instance.helperProportions=s.helperProportions,this.instance.offset.click=s.offset.click,this.instance._intersectsWith(this.instance.containerCache)&&(a=!0,e.each(s.sortables,function(){return this.instance.positionAbs=s.positionAbs,this.instance.helperProportions=s.helperProportions,this.instance.offset.click=s.offset.click,this!==o&&this.instance._intersectsWith(this.instance.containerCache)&&e.contains(o.instance.element[0],this.instance.element[0])&&(a=!1),a})),a?(this.instance.isOver||(this.instance.isOver=1,this.instance.currentItem=e(n).clone().removeAttr("id").appendTo(this.instance.element).data("ui-sortable-item",!0),this.instance.options._helper=this.instance.options.helper,this.instance.options.helper=function(){return i.helper[0]},t.target=this.instance.currentItem[0],this.instance._mouseCapture(t,!0),this.instance._mouseStart(t,!0,!0),this.instance.offset.click.top=s.offset.click.top,this.instance.offset.click.left=s.offset.click.left,this.instance.offset.parent.left-=s.offset.parent.left-this.instance.offset.parent.left,this.instance.offset.parent.top-=s.offset.parent.top-this.instance.offset.parent.top,s._trigger("toSortable",t),s.dropped=this.instance.element,s.currentItem=s.element,this.instance.fromOutside=s),this.instance.currentItem&&this.instance._mouseDrag(t)):this.instance.isOver&&(this.instance.isOver=0,this.instance.cancelHelperRemoval=!0,this.instance.options.revert=!1,this.instance._trigger("out",t,this.instance._uiHash(this.instance)),this.instance._mouseStop(t,!0),this.instance.options.helper=this.instance.options._helper,this.instance.currentItem.remove(),this.instance.placeholder&&this.instance.placeholder.remove(),s._trigger("fromSortable",t),s.dropped=!1)})}}),e.ui.plugin.add("draggable","cursor",{start:function(){var t=e("body"),i=e(this).data("ui-draggable").options;t.css("cursor")&&(i._cursor=t.css("cursor")),t.css("cursor",i.cursor)},stop:function(){var t=e(this).data("ui-draggable").options;t._cursor&&e("body").css("cursor",t._cursor)}}),e.ui.plugin.add("draggable","opacity",{start:function(t,i){var s=e(i.helper),n=e(this).data("ui-draggable").options;s.css("opacity")&&(n._opacity=s.css("opacity")),s.css("opacity",n.opacity)},stop:function(t,i){var s=e(this).data("ui-draggable").options;s._opacity&&e(i.helper).css("opacity",s._opacity)}}),e.ui.plugin.add("draggable","scroll",{start:function(){var t=e(this).data("ui-draggable");t.scrollParent[0]!==document&&"HTML"!==t.scrollParent[0].tagName&&(t.overflowOffset=t.scrollParent.offset())},drag:function(t){var i=e(this).data("ui-draggable"),s=i.options,n=!1;i.scrollParent[0]!==document&&"HTML"!==i.scrollParent[0].tagName?(s.axis&&"x"===s.axis||(i.overflowOffset.top+i.scrollParent[0].offsetHeight-t.pageY<s.scrollSensitivity?i.scrollParent[0].scrollTop=n=i.scrollParent[0].scrollTop+s.scrollSpeed:t.pageY-i.overflowOffset.top<s.scrollSensitivity&&(i.scrollParent[0].scrollTop=n=i.scrollParent[0].scrollTop-s.scrollSpeed)),s.axis&&"y"===s.axis||(i.overflowOffset.left+i.scrollParent[0].offsetWidth-t.pageX<s.scrollSensitivity?i.scrollParent[0].scrollLeft=n=i.scrollParent[0].scrollLeft+s.scrollSpeed:t.pageX-i.overflowOffset.left<s.scrollSensitivity&&(i.scrollParent[0].scrollLeft=n=i.scrollParent[0].scrollLeft-s.scrollSpeed))):(s.axis&&"x"===s.axis||(t.pageY-e(document).scrollTop()<s.scrollSensitivity?n=e(document).scrollTop(e(document).scrollTop()-s.scrollSpeed):e(window).height()-(t.pageY-e(document).scrollTop())<s.scrollSensitivity&&(n=e(document).scrollTop(e(document).scrollTop()+s.scrollSpeed))),s.axis&&"y"===s.axis||(t.pageX-e(document).scrollLeft()<s.scrollSensitivity?n=e(document).scrollLeft(e(document).scrollLeft()-s.scrollSpeed):e(window).width()-(t.pageX-e(document).scrollLeft())<s.scrollSensitivity&&(n=e(document).scrollLeft(e(document).scrollLeft()+s.scrollSpeed)))),n!==!1&&e.ui.ddmanager&&!s.dropBehaviour&&e.ui.ddmanager.prepareOffsets(i,t)}}),e.ui.plugin.add("draggable","snap",{start:function(){var t=e(this).data("ui-draggable"),i=t.options;t.snapElements=[],e(i.snap.constructor!==String?i.snap.items||":data(ui-draggable)":i.snap).each(function(){var i=e(this),s=i.offset();this!==t.element[0]&&t.snapElements.push({item:this,width:i.outerWidth(),height:i.outerHeight(),top:s.top,left:s.left})})},drag:function(t,i){var s,n,a,o,r,h,l,u,c,d,p=e(this).data("ui-draggable"),f=p.options,m=f.snapTolerance,g=i.offset.left,v=g+p.helperProportions.width,b=i.offset.top,y=b+p.helperProportions.height;for(c=p.snapElements.length-1;c>=0;c--)r=p.snapElements[c].left,h=r+p.snapElements[c].width,l=p.snapElements[c].top,u=l+p.snapElements[c].height,r-m>v||g>h+m||l-m>y||b>u+m||!e.contains(p.snapElements[c].item.ownerDocument,p.snapElements[c].item)?(p.snapElements[c].snapping&&p.options.snap.release&&p.options.snap.release.call(p.element,t,e.extend(p._uiHash(),{snapItem:p.snapElements[c].item})),p.snapElements[c].snapping=!1):("inner"!==f.snapMode&&(s=m>=Math.abs(l-y),n=m>=Math.abs(u-b),a=m>=Math.abs(r-v),o=m>=Math.abs(h-g),s&&(i.position.top=p._convertPositionTo("relative",{top:l-p.helperProportions.height,left:0}).top-p.margins.top),n&&(i.position.top=p._convertPositionTo("relative",{top:u,left:0}).top-p.margins.top),a&&(i.position.left=p._convertPositionTo("relative",{top:0,left:r-p.helperProportions.width}).left-p.margins.left),o&&(i.position.left=p._convertPositionTo("relative",{top:0,left:h}).left-p.margins.left)),d=s||n||a||o,"outer"!==f.snapMode&&(s=m>=Math.abs(l-b),n=m>=Math.abs(u-y),a=m>=Math.abs(r-g),o=m>=Math.abs(h-v),s&&(i.position.top=p._convertPositionTo("relative",{top:l,left:0}).top-p.margins.top),n&&(i.position.top=p._convertPositionTo("relative",{top:u-p.helperProportions.height,left:0}).top-p.margins.top),a&&(i.position.left=p._convertPositionTo("relative",{top:0,left:r}).left-p.margins.left),o&&(i.position.left=p._convertPositionTo("relative",{top:0,left:h-p.helperProportions.width}).left-p.margins.left)),!p.snapElements[c].snapping&&(s||n||a||o||d)&&p.options.snap.snap&&p.options.snap.snap.call(p.element,t,e.extend(p._uiHash(),{snapItem:p.snapElements[c].item})),p.snapElements[c].snapping=s||n||a||o||d)}}),e.ui.plugin.add("draggable","stack",{start:function(){var t,i=this.data("ui-draggable").options,s=e.makeArray(e(i.stack)).sort(function(t,i){return(parseInt(e(t).css("zIndex"),10)||0)-(parseInt(e(i).css("zIndex"),10)||0)});s.length&&(t=parseInt(e(s[0]).css("zIndex"),10)||0,e(s).each(function(i){e(this).css("zIndex",t+i)}),this.css("zIndex",t+s.length))}}),e.ui.plugin.add("draggable","zIndex",{start:function(t,i){var s=e(i.helper),n=e(this).data("ui-draggable").options;s.css("zIndex")&&(n._zIndex=s.css("zIndex")),s.css("zIndex",n.zIndex)},stop:function(t,i){var s=e(this).data("ui-draggable").options;s._zIndex&&e(i.helper).css("zIndex",s._zIndex)}})})(jQuery);(function(e){function t(e){return parseInt(e,10)||0}function i(e){return!isNaN(parseInt(e,10))}e.widget("ui.resizable",e.ui.mouse,{version:"1.10.3",widgetEventPrefix:"resize",options:{alsoResize:!1,animate:!1,animateDuration:"slow",animateEasing:"swing",aspectRatio:!1,autoHide:!1,containment:!1,ghost:!1,grid:!1,handles:"e,s,se",helper:!1,maxHeight:null,maxWidth:null,minHeight:10,minWidth:10,zIndex:90,resize:null,start:null,stop:null},_create:function(){var t,i,s,n,a,o=this,r=this.options;if(this.element.addClass("ui-resizable"),e.extend(this,{_aspectRatio:!!r.aspectRatio,aspectRatio:r.aspectRatio,originalElement:this.element,_proportionallyResizeElements:[],_helper:r.helper||r.ghost||r.animate?r.helper||"ui-resizable-helper":null}),this.element[0].nodeName.match(/canvas|textarea|input|select|button|img/i)&&(this.element.wrap(e("<div class='ui-wrapper' style='overflow: hidden;'></div>").css({position:this.element.css("position"),width:this.element.outerWidth(),height:this.element.outerHeight(),top:this.element.css("top"),left:this.element.css("left")})),this.element=this.element.parent().data("ui-resizable",this.element.data("ui-resizable")),this.elementIsWrapper=!0,this.element.css({marginLeft:this.originalElement.css("marginLeft"),marginTop:this.originalElement.css("marginTop"),marginRight:this.originalElement.css("marginRight"),marginBottom:this.originalElement.css("marginBottom")}),this.originalElement.css({marginLeft:0,marginTop:0,marginRight:0,marginBottom:0}),this.originalResizeStyle=this.originalElement.css("resize"),this.originalElement.css("resize","none"),this._proportionallyResizeElements.push(this.originalElement.css({position:"static",zoom:1,display:"block"})),this.originalElement.css({margin:this.originalElement.css("margin")}),this._proportionallyResize()),this.handles=r.handles||(e(".ui-resizable-handle",this.element).length?{n:".ui-resizable-n",e:".ui-resizable-e",s:".ui-resizable-s",w:".ui-resizable-w",se:".ui-resizable-se",sw:".ui-resizable-sw",ne:".ui-resizable-ne",nw:".ui-resizable-nw"}:"e,s,se"),this.handles.constructor===String)for("all"===this.handles&&(this.handles="n,e,s,w,se,sw,ne,nw"),t=this.handles.split(","),this.handles={},i=0;t.length>i;i++)s=e.trim(t[i]),a="ui-resizable-"+s,n=e("<div class='ui-resizable-handle "+a+"'></div>"),n.css({zIndex:r.zIndex}),"se"===s&&n.addClass("ui-icon ui-icon-gripsmall-diagonal-se"),this.handles[s]=".ui-resizable-"+s,this.element.append(n);this._renderAxis=function(t){var i,s,n,a;t=t||this.element;for(i in this.handles)this.handles[i].constructor===String&&(this.handles[i]=e(this.handles[i],this.element).show()),this.elementIsWrapper&&this.originalElement[0].nodeName.match(/textarea|input|select|button/i)&&(s=e(this.handles[i],this.element),a=/sw|ne|nw|se|n|s/.test(i)?s.outerHeight():s.outerWidth(),n=["padding",/ne|nw|n/.test(i)?"Top":/se|sw|s/.test(i)?"Bottom":/^e$/.test(i)?"Right":"Left"].join(""),t.css(n,a),this._proportionallyResize()),e(this.handles[i]).length},this._renderAxis(this.element),this._handles=e(".ui-resizable-handle",this.element).disableSelection(),this._handles.mouseover(function(){o.resizing||(this.className&&(n=this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i)),o.axis=n&&n[1]?n[1]:"se")}),r.autoHide&&(this._handles.hide(),e(this.element).addClass("ui-resizable-autohide").mouseenter(function(){r.disabled||(e(this).removeClass("ui-resizable-autohide"),o._handles.show())}).mouseleave(function(){r.disabled||o.resizing||(e(this).addClass("ui-resizable-autohide"),o._handles.hide())})),this._mouseInit()},_destroy:function(){this._mouseDestroy();var t,i=function(t){e(t).removeClass("ui-resizable ui-resizable-disabled ui-resizable-resizing").removeData("resizable").removeData("ui-resizable").unbind(".resizable").find(".ui-resizable-handle").remove()};return this.elementIsWrapper&&(i(this.element),t=this.element,this.originalElement.css({position:t.css("position"),width:t.outerWidth(),height:t.outerHeight(),top:t.css("top"),left:t.css("left")}).insertAfter(t),t.remove()),this.originalElement.css("resize",this.originalResizeStyle),i(this.originalElement),this},_mouseCapture:function(t){var i,s,n=!1;for(i in this.handles)s=e(this.handles[i])[0],(s===t.target||e.contains(s,t.target))&&(n=!0);return!this.options.disabled&&n},_mouseStart:function(i){var s,n,a,o=this.options,r=this.element.position(),h=this.element;return this.resizing=!0,/absolute/.test(h.css("position"))?h.css({position:"absolute",top:h.css("top"),left:h.css("left")}):h.is(".ui-draggable")&&h.css({position:"absolute",top:r.top,left:r.left}),this._renderProxy(),s=t(this.helper.css("left")),n=t(this.helper.css("top")),o.containment&&(s+=e(o.containment).scrollLeft()||0,n+=e(o.containment).scrollTop()||0),this.offset=this.helper.offset(),this.position={left:s,top:n},this.size=this._helper?{width:h.outerWidth(),height:h.outerHeight()}:{width:h.width(),height:h.height()},this.originalSize=this._helper?{width:h.outerWidth(),height:h.outerHeight()}:{width:h.width(),height:h.height()},this.originalPosition={left:s,top:n},this.sizeDiff={width:h.outerWidth()-h.width(),height:h.outerHeight()-h.height()},this.originalMousePosition={left:i.pageX,top:i.pageY},this.aspectRatio="number"==typeof o.aspectRatio?o.aspectRatio:this.originalSize.width/this.originalSize.height||1,a=e(".ui-resizable-"+this.axis).css("cursor"),e("body").css("cursor","auto"===a?this.axis+"-resize":a),h.addClass("ui-resizable-resizing"),this._propagate("start",i),!0},_mouseDrag:function(t){var i,s=this.helper,n={},a=this.originalMousePosition,o=this.axis,r=this.position.top,h=this.position.left,l=this.size.width,u=this.size.height,c=t.pageX-a.left||0,d=t.pageY-a.top||0,p=this._change[o];return p?(i=p.apply(this,[t,c,d]),this._updateVirtualBoundaries(t.shiftKey),(this._aspectRatio||t.shiftKey)&&(i=this._updateRatio(i,t)),i=this._respectSize(i,t),this._updateCache(i),this._propagate("resize",t),this.position.top!==r&&(n.top=this.position.top+"px"),this.position.left!==h&&(n.left=this.position.left+"px"),this.size.width!==l&&(n.width=this.size.width+"px"),this.size.height!==u&&(n.height=this.size.height+"px"),s.css(n),!this._helper&&this._proportionallyResizeElements.length&&this._proportionallyResize(),e.isEmptyObject(n)||this._trigger("resize",t,this.ui()),!1):!1},_mouseStop:function(t){this.resizing=!1;var i,s,n,a,o,r,h,l=this.options,u=this;return this._helper&&(i=this._proportionallyResizeElements,s=i.length&&/textarea/i.test(i[0].nodeName),n=s&&e.ui.hasScroll(i[0],"left")?0:u.sizeDiff.height,a=s?0:u.sizeDiff.width,o={width:u.helper.width()-a,height:u.helper.height()-n},r=parseInt(u.element.css("left"),10)+(u.position.left-u.originalPosition.left)||null,h=parseInt(u.element.css("top"),10)+(u.position.top-u.originalPosition.top)||null,l.animate||this.element.css(e.extend(o,{top:h,left:r})),u.helper.height(u.size.height),u.helper.width(u.size.width),this._helper&&!l.animate&&this._proportionallyResize()),e("body").css("cursor","auto"),this.element.removeClass("ui-resizable-resizing"),this._propagate("stop",t),this._helper&&this.helper.remove(),!1},_updateVirtualBoundaries:function(e){var t,s,n,a,o,r=this.options;o={minWidth:i(r.minWidth)?r.minWidth:0,maxWidth:i(r.maxWidth)?r.maxWidth:1/0,minHeight:i(r.minHeight)?r.minHeight:0,maxHeight:i(r.maxHeight)?r.maxHeight:1/0},(this._aspectRatio||e)&&(t=o.minHeight*this.aspectRatio,n=o.minWidth/this.aspectRatio,s=o.maxHeight*this.aspectRatio,a=o.maxWidth/this.aspectRatio,t>o.minWidth&&(o.minWidth=t),n>o.minHeight&&(o.minHeight=n),o.maxWidth>s&&(o.maxWidth=s),o.maxHeight>a&&(o.maxHeight=a)),this._vBoundaries=o},_updateCache:function(e){this.offset=this.helper.offset(),i(e.left)&&(this.position.left=e.left),i(e.top)&&(this.position.top=e.top),i(e.height)&&(this.size.height=e.height),i(e.width)&&(this.size.width=e.width)},_updateRatio:function(e){var t=this.position,s=this.size,n=this.axis;return i(e.height)?e.width=e.height*this.aspectRatio:i(e.width)&&(e.height=e.width/this.aspectRatio),"sw"===n&&(e.left=t.left+(s.width-e.width),e.top=null),"nw"===n&&(e.top=t.top+(s.height-e.height),e.left=t.left+(s.width-e.width)),e},_respectSize:function(e){var t=this._vBoundaries,s=this.axis,n=i(e.width)&&t.maxWidth&&t.maxWidth<e.width,a=i(e.height)&&t.maxHeight&&t.maxHeight<e.height,o=i(e.width)&&t.minWidth&&t.minWidth>e.width,r=i(e.height)&&t.minHeight&&t.minHeight>e.height,h=this.originalPosition.left+this.originalSize.width,l=this.position.top+this.size.height,u=/sw|nw|w/.test(s),c=/nw|ne|n/.test(s);return o&&(e.width=t.minWidth),r&&(e.height=t.minHeight),n&&(e.width=t.maxWidth),a&&(e.height=t.maxHeight),o&&u&&(e.left=h-t.minWidth),n&&u&&(e.left=h-t.maxWidth),r&&c&&(e.top=l-t.minHeight),a&&c&&(e.top=l-t.maxHeight),e.width||e.height||e.left||!e.top?e.width||e.height||e.top||!e.left||(e.left=null):e.top=null,e},_proportionallyResize:function(){if(this._proportionallyResizeElements.length){var e,t,i,s,n,a=this.helper||this.element;for(e=0;this._proportionallyResizeElements.length>e;e++){if(n=this._proportionallyResizeElements[e],!this.borderDif)for(this.borderDif=[],i=[n.css("borderTopWidth"),n.css("borderRightWidth"),n.css("borderBottomWidth"),n.css("borderLeftWidth")],s=[n.css("paddingTop"),n.css("paddingRight"),n.css("paddingBottom"),n.css("paddingLeft")],t=0;i.length>t;t++)this.borderDif[t]=(parseInt(i[t],10)||0)+(parseInt(s[t],10)||0);n.css({height:a.height()-this.borderDif[0]-this.borderDif[2]||0,width:a.width()-this.borderDif[1]-this.borderDif[3]||0})}}},_renderProxy:function(){var t=this.element,i=this.options;this.elementOffset=t.offset(),this._helper?(this.helper=this.helper||e("<div style='overflow:hidden;'></div>"),this.helper.addClass(this._helper).css({width:this.element.outerWidth()-1,height:this.element.outerHeight()-1,position:"absolute",left:this.elementOffset.left+"px",top:this.elementOffset.top+"px",zIndex:++i.zIndex}),this.helper.appendTo("body").disableSelection()):this.helper=this.element},_change:{e:function(e,t){return{width:this.originalSize.width+t}},w:function(e,t){var i=this.originalSize,s=this.originalPosition;return{left:s.left+t,width:i.width-t}},n:function(e,t,i){var s=this.originalSize,n=this.originalPosition;return{top:n.top+i,height:s.height-i}},s:function(e,t,i){return{height:this.originalSize.height+i}},se:function(t,i,s){return e.extend(this._change.s.apply(this,arguments),this._change.e.apply(this,[t,i,s]))},sw:function(t,i,s){return e.extend(this._change.s.apply(this,arguments),this._change.w.apply(this,[t,i,s]))},ne:function(t,i,s){return e.extend(this._change.n.apply(this,arguments),this._change.e.apply(this,[t,i,s]))},nw:function(t,i,s){return e.extend(this._change.n.apply(this,arguments),this._change.w.apply(this,[t,i,s]))}},_propagate:function(t,i){e.ui.plugin.call(this,t,[i,this.ui()]),"resize"!==t&&this._trigger(t,i,this.ui())},plugins:{},ui:function(){return{originalElement:this.originalElement,element:this.element,helper:this.helper,position:this.position,size:this.size,originalSize:this.originalSize,originalPosition:this.originalPosition}}}),e.ui.plugin.add("resizable","animate",{stop:function(t){var i=e(this).data("ui-resizable"),s=i.options,n=i._proportionallyResizeElements,a=n.length&&/textarea/i.test(n[0].nodeName),o=a&&e.ui.hasScroll(n[0],"left")?0:i.sizeDiff.height,r=a?0:i.sizeDiff.width,h={width:i.size.width-r,height:i.size.height-o},l=parseInt(i.element.css("left"),10)+(i.position.left-i.originalPosition.left)||null,u=parseInt(i.element.css("top"),10)+(i.position.top-i.originalPosition.top)||null;i.element.animate(e.extend(h,u&&l?{top:u,left:l}:{}),{duration:s.animateDuration,easing:s.animateEasing,step:function(){var s={width:parseInt(i.element.css("width"),10),height:parseInt(i.element.css("height"),10),top:parseInt(i.element.css("top"),10),left:parseInt(i.element.css("left"),10)};n&&n.length&&e(n[0]).css({width:s.width,height:s.height}),i._updateCache(s),i._propagate("resize",t)}})}}),e.ui.plugin.add("resizable","containment",{start:function(){var i,s,n,a,o,r,h,l=e(this).data("ui-resizable"),u=l.options,c=l.element,d=u.containment,p=d instanceof e?d.get(0):/parent/.test(d)?c.parent().get(0):d;p&&(l.containerElement=e(p),/document/.test(d)||d===document?(l.containerOffset={left:0,top:0},l.containerPosition={left:0,top:0},l.parentData={element:e(document),left:0,top:0,width:e(document).width(),height:e(document).height()||document.body.parentNode.scrollHeight}):(i=e(p),s=[],e(["Top","Right","Left","Bottom"]).each(function(e,n){s[e]=t(i.css("padding"+n))}),l.containerOffset=i.offset(),l.containerPosition=i.position(),l.containerSize={height:i.innerHeight()-s[3],width:i.innerWidth()-s[1]},n=l.containerOffset,a=l.containerSize.height,o=l.containerSize.width,r=e.ui.hasScroll(p,"left")?p.scrollWidth:o,h=e.ui.hasScroll(p)?p.scrollHeight:a,l.parentData={element:p,left:n.left,top:n.top,width:r,height:h}))},resize:function(t){var i,s,n,a,o=e(this).data("ui-resizable"),r=o.options,h=o.containerOffset,l=o.position,u=o._aspectRatio||t.shiftKey,c={top:0,left:0},d=o.containerElement;d[0]!==document&&/static/.test(d.css("position"))&&(c=h),l.left<(o._helper?h.left:0)&&(o.size.width=o.size.width+(o._helper?o.position.left-h.left:o.position.left-c.left),u&&(o.size.height=o.size.width/o.aspectRatio),o.position.left=r.helper?h.left:0),l.top<(o._helper?h.top:0)&&(o.size.height=o.size.height+(o._helper?o.position.top-h.top:o.position.top),u&&(o.size.width=o.size.height*o.aspectRatio),o.position.top=o._helper?h.top:0),o.offset.left=o.parentData.left+o.position.left,o.offset.top=o.parentData.top+o.position.top,i=Math.abs((o._helper?o.offset.left-c.left:o.offset.left-c.left)+o.sizeDiff.width),s=Math.abs((o._helper?o.offset.top-c.top:o.offset.top-h.top)+o.sizeDiff.height),n=o.containerElement.get(0)===o.element.parent().get(0),a=/relative|absolute/.test(o.containerElement.css("position")),n&&a&&(i-=o.parentData.left),i+o.size.width>=o.parentData.width&&(o.size.width=o.parentData.width-i,u&&(o.size.height=o.size.width/o.aspectRatio)),s+o.size.height>=o.parentData.height&&(o.size.height=o.parentData.height-s,u&&(o.size.width=o.size.height*o.aspectRatio))},stop:function(){var t=e(this).data("ui-resizable"),i=t.options,s=t.containerOffset,n=t.containerPosition,a=t.containerElement,o=e(t.helper),r=o.offset(),h=o.outerWidth()-t.sizeDiff.width,l=o.outerHeight()-t.sizeDiff.height;t._helper&&!i.animate&&/relative/.test(a.css("position"))&&e(this).css({left:r.left-n.left-s.left,width:h,height:l}),t._helper&&!i.animate&&/static/.test(a.css("position"))&&e(this).css({left:r.left-n.left-s.left,width:h,height:l})}}),e.ui.plugin.add("resizable","alsoResize",{start:function(){var t=e(this).data("ui-resizable"),i=t.options,s=function(t){e(t).each(function(){var t=e(this);t.data("ui-resizable-alsoresize",{width:parseInt(t.width(),10),height:parseInt(t.height(),10),left:parseInt(t.css("left"),10),top:parseInt(t.css("top"),10)})})};"object"!=typeof i.alsoResize||i.alsoResize.parentNode?s(i.alsoResize):i.alsoResize.length?(i.alsoResize=i.alsoResize[0],s(i.alsoResize)):e.each(i.alsoResize,function(e){s(e)})},resize:function(t,i){var s=e(this).data("ui-resizable"),n=s.options,a=s.originalSize,o=s.originalPosition,r={height:s.size.height-a.height||0,width:s.size.width-a.width||0,top:s.position.top-o.top||0,left:s.position.left-o.left||0},h=function(t,s){e(t).each(function(){var t=e(this),n=e(this).data("ui-resizable-alsoresize"),a={},o=s&&s.length?s:t.parents(i.originalElement[0]).length?["width","height"]:["width","height","top","left"];e.each(o,function(e,t){var i=(n[t]||0)+(r[t]||0);i&&i>=0&&(a[t]=i||null)}),t.css(a)})};"object"!=typeof n.alsoResize||n.alsoResize.nodeType?h(n.alsoResize):e.each(n.alsoResize,function(e,t){h(e,t)})},stop:function(){e(this).removeData("resizable-alsoresize")}}),e.ui.plugin.add("resizable","ghost",{start:function(){var t=e(this).data("ui-resizable"),i=t.options,s=t.size;t.ghost=t.originalElement.clone(),t.ghost.css({opacity:.25,display:"block",position:"relative",height:s.height,width:s.width,margin:0,left:0,top:0}).addClass("ui-resizable-ghost").addClass("string"==typeof i.ghost?i.ghost:""),t.ghost.appendTo(t.helper)},resize:function(){var t=e(this).data("ui-resizable");t.ghost&&t.ghost.css({position:"relative",height:t.size.height,width:t.size.width})},stop:function(){var t=e(this).data("ui-resizable");t.ghost&&t.helper&&t.helper.get(0).removeChild(t.ghost.get(0))}}),e.ui.plugin.add("resizable","grid",{resize:function(){var t=e(this).data("ui-resizable"),i=t.options,s=t.size,n=t.originalSize,a=t.originalPosition,o=t.axis,r="number"==typeof i.grid?[i.grid,i.grid]:i.grid,h=r[0]||1,l=r[1]||1,u=Math.round((s.width-n.width)/h)*h,c=Math.round((s.height-n.height)/l)*l,d=n.width+u,p=n.height+c,f=i.maxWidth&&d>i.maxWidth,m=i.maxHeight&&p>i.maxHeight,g=i.minWidth&&i.minWidth>d,v=i.minHeight&&i.minHeight>p;i.grid=r,g&&(d+=h),v&&(p+=l),f&&(d-=h),m&&(p-=l),/^(se|s|e)$/.test(o)?(t.size.width=d,t.size.height=p):/^(ne)$/.test(o)?(t.size.width=d,t.size.height=p,t.position.top=a.top-c):/^(sw)$/.test(o)?(t.size.width=d,t.size.height=p,t.position.left=a.left-u):(t.size.width=d,t.size.height=p,t.position.top=a.top-c,t.position.left=a.left-u)}})})(jQuery);(function(t){var e,i,s,n,a="ui-button ui-widget ui-state-default ui-corner-all",o="ui-state-hover ui-state-active ",r="ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon-primary ui-button-text-icon-secondary ui-button-text-only",h=function(){var e=t(this);setTimeout(function(){e.find(":ui-button").button("refresh")},1)},l=function(e){var i=e.name,s=e.form,n=t([]);return i&&(i=i.replace(/'/g,"\\'"),n=s?t(s).find("[name='"+i+"']"):t("[name='"+i+"']",e.ownerDocument).filter(function(){return!this.form})),n};t.widget("ui.button",{version:"1.10.3",defaultElement:"<button>",options:{disabled:null,text:!0,label:null,icons:{primary:null,secondary:null}},_create:function(){this.element.closest("form").unbind("reset"+this.eventNamespace).bind("reset"+this.eventNamespace,h),"boolean"!=typeof this.options.disabled?this.options.disabled=!!this.element.prop("disabled"):this.element.prop("disabled",this.options.disabled),this._determineButtonType(),this.hasTitle=!!this.buttonElement.attr("title");var o=this,r=this.options,c="checkbox"===this.type||"radio"===this.type,u=c?"":"ui-state-active",d="ui-state-focus";null===r.label&&(r.label="input"===this.type?this.buttonElement.val():this.buttonElement.html()),this._hoverable(this.buttonElement),this.buttonElement.addClass(a).attr("role","button").bind("mouseenter"+this.eventNamespace,function(){r.disabled||this===e&&t(this).addClass("ui-state-active")}).bind("mouseleave"+this.eventNamespace,function(){r.disabled||t(this).removeClass(u)}).bind("click"+this.eventNamespace,function(t){r.disabled&&(t.preventDefault(),t.stopImmediatePropagation())}),this.element.bind("focus"+this.eventNamespace,function(){o.buttonElement.addClass(d)}).bind("blur"+this.eventNamespace,function(){o.buttonElement.removeClass(d)}),c&&(this.element.bind("change"+this.eventNamespace,function(){n||o.refresh()}),this.buttonElement.bind("mousedown"+this.eventNamespace,function(t){r.disabled||(n=!1,i=t.pageX,s=t.pageY)}).bind("mouseup"+this.eventNamespace,function(t){r.disabled||(i!==t.pageX||s!==t.pageY)&&(n=!0)})),"checkbox"===this.type?this.buttonElement.bind("click"+this.eventNamespace,function(){return r.disabled||n?!1:undefined}):"radio"===this.type?this.buttonElement.bind("click"+this.eventNamespace,function(){if(r.disabled||n)return!1;t(this).addClass("ui-state-active"),o.buttonElement.attr("aria-pressed","true");var e=o.element[0];l(e).not(e).map(function(){return t(this).button("widget")[0]}).removeClass("ui-state-active").attr("aria-pressed","false")}):(this.buttonElement.bind("mousedown"+this.eventNamespace,function(){return r.disabled?!1:(t(this).addClass("ui-state-active"),e=this,o.document.one("mouseup",function(){e=null}),undefined)}).bind("mouseup"+this.eventNamespace,function(){return r.disabled?!1:(t(this).removeClass("ui-state-active"),undefined)}).bind("keydown"+this.eventNamespace,function(e){return r.disabled?!1:((e.keyCode===t.ui.keyCode.SPACE||e.keyCode===t.ui.keyCode.ENTER)&&t(this).addClass("ui-state-active"),undefined)}).bind("keyup"+this.eventNamespace+" blur"+this.eventNamespace,function(){t(this).removeClass("ui-state-active")}),this.buttonElement.is("a")&&this.buttonElement.keyup(function(e){e.keyCode===t.ui.keyCode.SPACE&&t(this).click()})),this._setOption("disabled",r.disabled),this._resetButton()},_determineButtonType:function(){var t,e,i;this.type=this.element.is("[type=checkbox]")?"checkbox":this.element.is("[type=radio]")?"radio":this.element.is("input")?"input":"button","checkbox"===this.type||"radio"===this.type?(t=this.element.parents().last(),e="label[for='"+this.element.attr("id")+"']",this.buttonElement=t.find(e),this.buttonElement.length||(t=t.length?t.siblings():this.element.siblings(),this.buttonElement=t.filter(e),this.buttonElement.length||(this.buttonElement=t.find(e))),this.element.addClass("ui-helper-hidden-accessible"),i=this.element.is(":checked"),i&&this.buttonElement.addClass("ui-state-active"),this.buttonElement.prop("aria-pressed",i)):this.buttonElement=this.element},widget:function(){return this.buttonElement},_destroy:function(){this.element.removeClass("ui-helper-hidden-accessible"),this.buttonElement.removeClass(a+" "+o+" "+r).removeAttr("role").removeAttr("aria-pressed").html(this.buttonElement.find(".ui-button-text").html()),this.hasTitle||this.buttonElement.removeAttr("title")},_setOption:function(t,e){return this._super(t,e),"disabled"===t?(e?this.element.prop("disabled",!0):this.element.prop("disabled",!1),undefined):(this._resetButton(),undefined)},refresh:function(){var e=this.element.is("input, button")?this.element.is(":disabled"):this.element.hasClass("ui-button-disabled");e!==this.options.disabled&&this._setOption("disabled",e),"radio"===this.type?l(this.element[0]).each(function(){t(this).is(":checked")?t(this).button("widget").addClass("ui-state-active").attr("aria-pressed","true"):t(this).button("widget").removeClass("ui-state-active").attr("aria-pressed","false")}):"checkbox"===this.type&&(this.element.is(":checked")?this.buttonElement.addClass("ui-state-active").attr("aria-pressed","true"):this.buttonElement.removeClass("ui-state-active").attr("aria-pressed","false"))},_resetButton:function(){if("input"===this.type)return this.options.label&&this.element.val(this.options.label),undefined;var e=this.buttonElement.removeClass(r),i=t("<span></span>",this.document[0]).addClass("ui-button-text").html(this.options.label).appendTo(e.empty()).text(),s=this.options.icons,n=s.primary&&s.secondary,a=[];s.primary||s.secondary?(this.options.text&&a.push("ui-button-text-icon"+(n?"s":s.primary?"-primary":"-secondary")),s.primary&&e.prepend("<span class='ui-button-icon-primary ui-icon "+s.primary+"'></span>"),s.secondary&&e.append("<span class='ui-button-icon-secondary ui-icon "+s.secondary+"'></span>"),this.options.text||(a.push(n?"ui-button-icons-only":"ui-button-icon-only"),this.hasTitle||e.attr("title",t.trim(i)))):a.push("ui-button-text-only"),e.addClass(a.join(" "))}}),t.widget("ui.buttonset",{version:"1.10.3",options:{items:"button, input[type=button], input[type=submit], input[type=reset], input[type=checkbox], input[type=radio], a, :data(ui-button)"},_create:function(){this.element.addClass("ui-buttonset")},_init:function(){this.refresh()},_setOption:function(t,e){"disabled"===t&&this.buttons.button("option",t,e),this._super(t,e)},refresh:function(){var e="rtl"===this.element.css("direction");this.buttons=this.element.find(this.options.items).filter(":ui-button").button("refresh").end().not(":ui-button").button().end().map(function(){return t(this).button("widget")[0]}).removeClass("ui-corner-all ui-corner-left ui-corner-right").filter(":first").addClass(e?"ui-corner-right":"ui-corner-left").end().filter(":last").addClass(e?"ui-corner-left":"ui-corner-right").end().end()},_destroy:function(){this.element.removeClass("ui-buttonset"),this.buttons.map(function(){return t(this).button("widget")[0]}).removeClass("ui-corner-left ui-corner-right").end().button("destroy")}})})(jQuery);(function(t){var e={buttons:!0,height:!0,maxHeight:!0,maxWidth:!0,minHeight:!0,minWidth:!0,width:!0},i={maxHeight:!0,maxWidth:!0,minHeight:!0,minWidth:!0};t.widget("ui.dialog",{version:"1.10.3",options:{appendTo:"body",autoOpen:!0,buttons:[],closeOnEscape:!0,closeText:"close",dialogClass:"",draggable:!0,hide:null,height:"auto",maxHeight:null,maxWidth:null,minHeight:150,minWidth:150,modal:!1,position:{my:"center",at:"center",of:window,collision:"fit",using:function(e){var i=t(this).css(e).offset().top;0>i&&t(this).css("top",e.top-i)}},resizable:!0,show:null,title:null,width:300,beforeClose:null,close:null,drag:null,dragStart:null,dragStop:null,focus:null,open:null,resize:null,resizeStart:null,resizeStop:null},_create:function(){this.originalCss={display:this.element[0].style.display,width:this.element[0].style.width,minHeight:this.element[0].style.minHeight,maxHeight:this.element[0].style.maxHeight,height:this.element[0].style.height},this.originalPosition={parent:this.element.parent(),index:this.element.parent().children().index(this.element)},this.originalTitle=this.element.attr("title"),this.options.title=this.options.title||this.originalTitle,this._createWrapper(),this.element.show().removeAttr("title").addClass("ui-dialog-content ui-widget-content").appendTo(this.uiDialog),this._createTitlebar(),this._createButtonPane(),this.options.draggable&&t.fn.draggable&&this._makeDraggable(),this.options.resizable&&t.fn.resizable&&this._makeResizable(),this._isOpen=!1},_init:function(){this.options.autoOpen&&this.open()},_appendTo:function(){var e=this.options.appendTo;return e&&(e.jquery||e.nodeType)?t(e):this.document.find(e||"body").eq(0)},_destroy:function(){var t,e=this.originalPosition;this._destroyOverlay(),this.element.removeUniqueId().removeClass("ui-dialog-content ui-widget-content").css(this.originalCss).detach(),this.uiDialog.stop(!0,!0).remove(),this.originalTitle&&this.element.attr("title",this.originalTitle),t=e.parent.children().eq(e.index),t.length&&t[0]!==this.element[0]?t.before(this.element):e.parent.append(this.element)},widget:function(){return this.uiDialog},disable:t.noop,enable:t.noop,close:function(e){var i=this;this._isOpen&&this._trigger("beforeClose",e)!==!1&&(this._isOpen=!1,this._destroyOverlay(),this.opener.filter(":focusable").focus().length||t(this.document[0].activeElement).blur(),this._hide(this.uiDialog,this.options.hide,function(){i._trigger("close",e)}))},isOpen:function(){return this._isOpen},moveToTop:function(){this._moveToTop()},_moveToTop:function(t,e){var i=!!this.uiDialog.nextAll(":visible").insertBefore(this.uiDialog).length;return i&&!e&&this._trigger("focus",t),i},open:function(){var e=this;return this._isOpen?(this._moveToTop()&&this._focusTabbable(),undefined):(this._isOpen=!0,this.opener=t(this.document[0].activeElement),this._size(),this._position(),this._createOverlay(),this._moveToTop(null,!0),this._show(this.uiDialog,this.options.show,function(){e._focusTabbable(),e._trigger("focus")}),this._trigger("open"),undefined)},_focusTabbable:function(){var t=this.element.find("[autofocus]");t.length||(t=this.element.find(":tabbable")),t.length||(t=this.uiDialogButtonPane.find(":tabbable")),t.length||(t=this.uiDialogTitlebarClose.filter(":tabbable")),t.length||(t=this.uiDialog),t.eq(0).focus()},_keepFocus:function(e){function i(){var e=this.document[0].activeElement,i=this.uiDialog[0]===e||t.contains(this.uiDialog[0],e);i||this._focusTabbable()}e.preventDefault(),i.call(this),this._delay(i)},_createWrapper:function(){this.uiDialog=t("<div>").addClass("ui-dialog ui-widget ui-widget-content ui-corner-all ui-front "+this.options.dialogClass).hide().attr({tabIndex:-1,role:"dialog"}).appendTo(this._appendTo()),this._on(this.uiDialog,{keydown:function(e){if(this.options.closeOnEscape&&!e.isDefaultPrevented()&&e.keyCode&&e.keyCode===t.ui.keyCode.ESCAPE)return e.preventDefault(),this.close(e),undefined;if(e.keyCode===t.ui.keyCode.TAB){var i=this.uiDialog.find(":tabbable"),s=i.filter(":first"),n=i.filter(":last");e.target!==n[0]&&e.target!==this.uiDialog[0]||e.shiftKey?e.target!==s[0]&&e.target!==this.uiDialog[0]||!e.shiftKey||(n.focus(1),e.preventDefault()):(s.focus(1),e.preventDefault())}},mousedown:function(t){this._moveToTop(t)&&this._focusTabbable()}}),this.element.find("[aria-describedby]").length||this.uiDialog.attr({"aria-describedby":this.element.uniqueId().attr("id")})},_createTitlebar:function(){var e;this.uiDialogTitlebar=t("<div>").addClass("ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix").prependTo(this.uiDialog),this._on(this.uiDialogTitlebar,{mousedown:function(e){t(e.target).closest(".ui-dialog-titlebar-close")||this.uiDialog.focus()}}),this.uiDialogTitlebarClose=t("<button></button>").button({label:this.options.closeText,icons:{primary:"ui-icon-closethick"},text:!1}).addClass("ui-dialog-titlebar-close").appendTo(this.uiDialogTitlebar),this._on(this.uiDialogTitlebarClose,{click:function(t){t.preventDefault(),this.close(t)}}),e=t("<span>").uniqueId().addClass("ui-dialog-title").prependTo(this.uiDialogTitlebar),this._title(e),this.uiDialog.attr({"aria-labelledby":e.attr("id")})},_title:function(t){this.options.title||t.html("&#160;"),t.text(this.options.title)},_createButtonPane:function(){this.uiDialogButtonPane=t("<div>").addClass("ui-dialog-buttonpane ui-widget-content ui-helper-clearfix"),this.uiButtonSet=t("<div>").addClass("ui-dialog-buttonset").appendTo(this.uiDialogButtonPane),this._createButtons()},_createButtons:function(){var e=this,i=this.options.buttons;return this.uiDialogButtonPane.remove(),this.uiButtonSet.empty(),t.isEmptyObject(i)||t.isArray(i)&&!i.length?(this.uiDialog.removeClass("ui-dialog-buttons"),undefined):(t.each(i,function(i,s){var n,a;s=t.isFunction(s)?{click:s,text:i}:s,s=t.extend({type:"button"},s),n=s.click,s.click=function(){n.apply(e.element[0],arguments)},a={icons:s.icons,text:s.showText},delete s.icons,delete s.showText,t("<button></button>",s).button(a).appendTo(e.uiButtonSet)}),this.uiDialog.addClass("ui-dialog-buttons"),this.uiDialogButtonPane.appendTo(this.uiDialog),undefined)},_makeDraggable:function(){function e(t){return{position:t.position,offset:t.offset}}var i=this,s=this.options;this.uiDialog.draggable({cancel:".ui-dialog-content, .ui-dialog-titlebar-close",handle:".ui-dialog-titlebar",containment:"document",start:function(s,n){t(this).addClass("ui-dialog-dragging"),i._blockFrames(),i._trigger("dragStart",s,e(n))},drag:function(t,s){i._trigger("drag",t,e(s))},stop:function(n,a){s.position=[a.position.left-i.document.scrollLeft(),a.position.top-i.document.scrollTop()],t(this).removeClass("ui-dialog-dragging"),i._unblockFrames(),i._trigger("dragStop",n,e(a))}})},_makeResizable:function(){function e(t){return{originalPosition:t.originalPosition,originalSize:t.originalSize,position:t.position,size:t.size}}var i=this,s=this.options,n=s.resizable,a=this.uiDialog.css("position"),o="string"==typeof n?n:"n,e,s,w,se,sw,ne,nw";this.uiDialog.resizable({cancel:".ui-dialog-content",containment:"document",alsoResize:this.element,maxWidth:s.maxWidth,maxHeight:s.maxHeight,minWidth:s.minWidth,minHeight:this._minHeight(),handles:o,start:function(s,n){t(this).addClass("ui-dialog-resizing"),i._blockFrames(),i._trigger("resizeStart",s,e(n))},resize:function(t,s){i._trigger("resize",t,e(s))},stop:function(n,a){s.height=t(this).height(),s.width=t(this).width(),t(this).removeClass("ui-dialog-resizing"),i._unblockFrames(),i._trigger("resizeStop",n,e(a))}}).css("position",a)},_minHeight:function(){var t=this.options;return"auto"===t.height?t.minHeight:Math.min(t.minHeight,t.height)},_position:function(){var t=this.uiDialog.is(":visible");t||this.uiDialog.show(),this.uiDialog.position(this.options.position),t||this.uiDialog.hide()},_setOptions:function(s){var n=this,a=!1,o={};t.each(s,function(t,s){n._setOption(t,s),t in e&&(a=!0),t in i&&(o[t]=s)}),a&&(this._size(),this._position()),this.uiDialog.is(":data(ui-resizable)")&&this.uiDialog.resizable("option",o)},_setOption:function(t,e){var i,s,n=this.uiDialog;"dialogClass"===t&&n.removeClass(this.options.dialogClass).addClass(e),"disabled"!==t&&(this._super(t,e),"appendTo"===t&&this.uiDialog.appendTo(this._appendTo()),"buttons"===t&&this._createButtons(),"closeText"===t&&this.uiDialogTitlebarClose.button({label:""+e}),"draggable"===t&&(i=n.is(":data(ui-draggable)"),i&&!e&&n.draggable("destroy"),!i&&e&&this._makeDraggable()),"position"===t&&this._position(),"resizable"===t&&(s=n.is(":data(ui-resizable)"),s&&!e&&n.resizable("destroy"),s&&"string"==typeof e&&n.resizable("option","handles",e),s||e===!1||this._makeResizable()),"title"===t&&this._title(this.uiDialogTitlebar.find(".ui-dialog-title")))},_size:function(){var t,e,i,s=this.options;this.element.show().css({width:"auto",minHeight:0,maxHeight:"none",height:0}),s.minWidth>s.width&&(s.width=s.minWidth),t=this.uiDialog.css({height:"auto",width:s.width}).outerHeight(),e=Math.max(0,s.minHeight-t),i="number"==typeof s.maxHeight?Math.max(0,s.maxHeight-t):"none","auto"===s.height?this.element.css({minHeight:e,maxHeight:i,height:"auto"}):this.element.height(Math.max(0,s.height-t)),this.uiDialog.is(":data(ui-resizable)")&&this.uiDialog.resizable("option","minHeight",this._minHeight())},_blockFrames:function(){this.iframeBlocks=this.document.find("iframe").map(function(){var e=t(this);return t("<div>").css({position:"absolute",width:e.outerWidth(),height:e.outerHeight()}).appendTo(e.parent()).offset(e.offset())[0]})},_unblockFrames:function(){this.iframeBlocks&&(this.iframeBlocks.remove(),delete this.iframeBlocks)},_allowInteraction:function(e){return t(e.target).closest(".ui-dialog").length?!0:!!t(e.target).closest(".ui-datepicker").length},_createOverlay:function(){if(this.options.modal){var e=this,i=this.widgetFullName;t.ui.dialog.overlayInstances||this._delay(function(){t.ui.dialog.overlayInstances&&this.document.bind("focusin.dialog",function(s){e._allowInteraction(s)||(s.preventDefault(),t(".ui-dialog:visible:last .ui-dialog-content").data(i)._focusTabbable())})}),this.overlay=t("<div>").addClass("ui-widget-overlay ui-front").appendTo(this._appendTo()),this._on(this.overlay,{mousedown:"_keepFocus"}),t.ui.dialog.overlayInstances++}},_destroyOverlay:function(){this.options.modal&&this.overlay&&(t.ui.dialog.overlayInstances--,t.ui.dialog.overlayInstances||this.document.unbind("focusin.dialog"),this.overlay.remove(),this.overlay=null)}}),t.ui.dialog.overlayInstances=0,t.uiBackCompat!==!1&&t.widget("ui.dialog",t.ui.dialog,{_position:function(){var e,i=this.options.position,s=[],n=[0,0];i?(("string"==typeof i||"object"==typeof i&&"0"in i)&&(s=i.split?i.split(" "):[i[0],i[1]],1===s.length&&(s[1]=s[0]),t.each(["left","top"],function(t,e){+s[t]===s[t]&&(n[t]=s[t],s[t]=e)}),i={my:s[0]+(0>n[0]?n[0]:"+"+n[0])+" "+s[1]+(0>n[1]?n[1]:"+"+n[1]),at:s.join(" ")}),i=t.extend({},t.ui.dialog.prototype.options.position,i)):i=t.ui.dialog.prototype.options.position,e=this.uiDialog.is(":visible"),e||this.uiDialog.show(),this.uiDialog.position(i),e||this.uiDialog.hide()}})})(jQuery);

// jQuery UI Touch Punch

/*
 * jQuery UI Touch Punch 0.2.2
 *
 * Copyright 2011, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
(function(b){b.support.touch="ontouchend" in document;if(!b.support.touch){return;}var c=b.ui.mouse.prototype,e=c._mouseInit,a;function d(g,h){if(g.originalEvent.touches.length>1){return;}g.preventDefault();var i=g.originalEvent.changedTouches[0],f=document.createEvent("MouseEvents");f.initMouseEvent(h,true,true,window,1,i.screenX,i.screenY,i.clientX,i.clientY,false,false,false,false,0,null);g.target.dispatchEvent(f);}c._touchStart=function(g){var f=this;if(a||!f._mouseCapture(g.originalEvent.changedTouches[0])){return;}a=true;f._touchMoved=false;d(g,"mouseover");d(g,"mousemove");d(g,"mousedown");};c._touchMove=function(f){if(!a){return;}this._touchMoved=true;d(f,"mousemove");};c._touchEnd=function(f){if(!a){return;}d(f,"mouseup");d(f,"mouseout");if(!this._touchMoved){d(f,"click");}a=false;};c._mouseInit=function(){var f=this;f.element.bind("touchstart",b.proxy(f,"_touchStart")).bind("touchmove",b.proxy(f,"_touchMove")).bind("touchend",b.proxy(f,"_touchEnd"));e.call(f);};})(jQuery);