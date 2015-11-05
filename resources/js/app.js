

// App.js

App = {};

/**
 * Takes a dot-separated string of nested namespaces, creating any that do not already exist.
 * Example: App.namespace('Foo.Bar') creates window.Foo = {Bar: {}};
 * @param {string} aNamespace A dot-separated list of nested namespaces, e.g., 'Foo.Bar.Baz'
 */
App.namespace = function (aNamespace) {
	var myParts = aNamespace.split('.'),
		myNumParts = myParts.length,
		myCurrentNamespaceObject,
		myNextNamespace,
		i;

	myCurrentNamespaceObject = window;
	for (i = 0; i < myNumParts; i++) {
		myNextNamespace = myParts[i];
		if (typeof myCurrentNamespaceObject[myNextNamespace] === 'undefined') {
			myCurrentNamespaceObject[myNextNamespace] = {};
		}
		myCurrentNamespaceObject = myCurrentNamespaceObject[myNextNamespace];
	}
};

/**
 * Create an instance (child) of the object `parent`. `parent` will be the prototype of the returned object.
 * @param parent
 * @param {Object} [options] A list of arguments to pass to the child's `init` method, if it exists.
 */
App.create = function (parent, options) {
	var child = Object.create(parent);
	child.prototype = parent;
	if (typeof child.init === 'function') {
		child.init(options);
	}
	return child;
};

App.launch = function () {
	App.data.Page.loadPageData();
};

App.emptyFn = function () {};

/**
 * WARNING: not tested with recursive replacement (i.e., replacement values containing '{' and '}' characters) or
 * with characters that have special meaning to RegExp!
 * @param {String} template A template where, for example, "{myVar}" will be replaced with the value of values['myVar']
 * @param {Object} values A set of key/value pairs where the keys will be wrapped in {} and substituted into the tmpl.
 */
App.template = function (template, values) {
	var search,
		value;

	template = template ? template : '';

	for (value in values) {
		if (values.hasOwnProperty(value)) {
			search = new RegExp('{' + value + '}', 'g');
			template = template.replace(search, values[value]);
		}
	}
	return template;
};

/**
 * Removes any preceding or trailing whitespace characters from the given string, where whitespace is defined
 * by the \s character in JavaScript's RegExp support.
 * NOTE: JavaScript 1.8.1+ has String.prototype.trim(), but this will work everywhere.
 * @param {String} string The string to trim
 * @returns {String}
 */
App.trim = function (string) {
	return string.replace(/^\s+/, '').replace(/\s+$/, '');
};

App.Logger = {
	/**
	 * If the console object exists, pass the #log, #error, and #warn methods through; otherwise,
	 * they will retain their default no-op behavior.
	 * @returns {App.Logger} Returns itself so we can call #init on an object literal and capture the result.
	 */
	init: function (console) {
		if (!console) {
			return this;
		}
		if (console.log) {
			this.log = function () {
				console.log.apply(console, arguments);
			}
		}
		if (console.error) {
			this.error = function () {
				console.error.apply(console, arguments);
			}
		}
		if (console.warn) {
			this.warn = function () {
				console.warn.apply(console, arguments);
			}
		}

		return this;
	},

	log: App.emptyFn,

	error: App.emptyFn,

	warn: App.emptyFn
}.init(console);

// Concat.js

App.namespace('App.data.interaction');

App.data.interaction.Interaction = {
	id: null,
	startTime: null,
	stopTime: null,
	numCorrect: null,
	// {string|null|undefined} If `prompt` contains a string, it will be displayed at the top of the dialog.
	prompt: null,
	title: null,

    // TODO not sure if the interaction data model should know anything about its dialog view.
    dialog: null,

	init: function (interactionConfig) {
		this.id = interactionConfig.id;
		this.audio = interactionConfig.audio || null;
		this.prompt = interactionConfig.prompt || null;
		this.title = interactionConfig.windowTitle || null;
	},

	load: function () {
	},

	save: function () {
        this.computeNumCorrect();
		App.data.LocalStorage.save(this.id, this.userResponse);
		App.data.BlueEarth.submit(this);
	},

    computeNumCorrect: function () {
        App.Logger.log('Should override in subclass.');
        return null;
    },

	/**
	 * Get the length of time the user spent on this interaction.
	 * @returns {number} The number of seconds elapsed.
	 */
	getDuration: function () {
		var milliseconds;

		if (!this.startTime || !this.stopTime) {
			return null;
		}

		milliseconds = this.stopTime - this.startTime;

		return milliseconds < 0 ? 0 : Math.floor(milliseconds / 1000);
	},

	getUserResponse: function () {
		return this.userResponse;
	},
	setUserResponse: function (response) {
		this.userResponse = response;
	}
};
App.namespace('App.data.interaction.answerable');

App.data.interaction.answerable.AnswerableInteraction = (function () {
	var parent = App.data.interaction.Interaction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		App.data.interaction.Interaction.init.call(this, interactionConfig);

		this.gradable = interactionConfig.gradable || false;
		this.questions = interactionConfig.questions;

        /**
         * The book-supplied correct answers for the questions in this interaction.
         * @type {interaction.answers|*|answers|pageData.answers|dialog.answers|view.answers}
         */
		this.answers = interactionConfig.answers || [];
		this.userResponse = [];
		this.type = interactionConfig.type;
	};

	exports.load = function () {
		this.userResponse = App.data.LocalStorage.load(this.id) || [];
	};

	exports.checkAnswer = function (questionOrAnswerNumber, response) {
		var answer = this.answers[questionOrAnswerNumber],
			responseType = typeof response,
            processedAnswer,
			processedResponse;

        if (!this.gradable) {
			// If it's some kind of empty response, return false: empty = incorrect
			if ((response === null) ||
				(responseType === 'string' && response.length === 0) ||
				(responseType === 'number') && response < 0) {
				return false;
			}
			// Otherwise, it's not empty, so consider it to be correct.
			return true;
		}

		if (typeof answer === 'string') {
			processedAnswer = App.trim(answer.toLowerCase()).split('â€™').join('\'');
			processedResponse = App.trim(response.toLowerCase()).split('â€™').join('\'');
			return processedAnswer === processedResponse;
		}
		return answer === response;
	};

	return exports;
}());
App.namespace('App.data.interaction.answerable.multifield');

App.data.interaction.answerable.multifield.MultifieldAnswerableInteraction = (function () {
	var parent = App.data.interaction.answerable.AnswerableInteraction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		parent.init.call(this, interactionConfig);

		// TODO: this shouldn't be necessary. Rather than store displayable questions here, move it to the view/dialog.
		this.originalQuestions = interactionConfig.questions;
		this.originalAnswers = interactionConfig.answers;

		if (this.originalQuestions.length !== this.originalAnswers.length) {
			App.Logger.error('MultifieldAnswerableInteraction#init: bad input data; number of questions does not match number of answers.');
		}
	 };

	/**
	 * @private
	 * @param {number} questionNumber The index of the question (or answer or userResponse, should be 1-to-1-to-1).
	 * @returns {string} The unique response ID based on the interaction ID and the response index.
	 */
	exports.getResponseId = function (questionNumber) {
		return App.template('{interactionId}-{questionNumber}', {
			interactionId: this.id,
			questionNumber: questionNumber
		});
	};

	// @Override in subclasses
	exports._processValue = function (value) {
		return value;
	};

	// TODO merge this with AnswerableInteraction#checkAnswer. Probably need some kind of proper TextField class.
	exports.checkAnswer = function (questionOrAnswerNumber, responseParts) {
		var answerParts = this.originalAnswers[questionOrAnswerNumber],
			numAnswerParts = answerParts.length,
			answerPart,
			responsePart,
			i;

		for (i = 0; i < numAnswerParts; i++) {
			answerPart = this._processValue(answerParts[i]);
			responsePart = this._processValue(responseParts[i]);
			if (answerPart !== responsePart) {
				return false;
			}
		}
		return true;
	};

	exports.assembleSentence = function () {
		// TODO: pull this method up from the child class
		return App.data.interaction.answerable.multifield.FillTheBlankInteraction.assembleSentence.apply(this, arguments);
	};

	return exports;
}());
App.namespace('App.data');

App.data.BlueEarth = {
	/**
	 * Whether or not we are running in a BlueEarth iframe.
	 * Computed once; see below this object/singleton definition.
	 * @type boolean
	 * TODO make this a function
	 */
	isPresent: null,

	submit: function (interaction) {
        var pageData,
            bookID,
            userResponse = interaction.getUserResponse();

		// TODO replace this with a check on this.isPresent
        if (typeof parent.FrameInterface !== 'object' || typeof parent.FrameInterface.setActivityData !== 'function') {
            return;
        }

        pageData = window.pageData ? window.pageData : null;
        bookID = pageData && pageData.bookID ? pageData.bookID : null;
        parent.FrameInterface.setActivityData({
            book_id: bookID ? bookID : null,
			interaction_id: interaction.id,
			duration: interaction.getDuration(),
			score: interaction.numCorrect,
            given_answers: typeof userResponse === 'string' ? [userResponse] : userResponse
		});
	},

    openActivity: function (interaction) { //TO-DO - Clean up. This is hacky. This allows BEI to open dialogs externally
        var pageData = window.pageData ? window.pageData : null,
            i;

        for (i = 0; i < pageData.interactions.length; i++) { //cycle through all activities
            if (pageData.interactions[i].id == interaction) { //found specified interaction
                pageData.interactions[i].callback(); //call function stored during creation
                break;
            }
        }
    }
};

if (parent.FrameInterface && typeof parent.FrameInterface.setActivityData === 'function') {
	App.data.BlueEarth.isPresent = true;
} else {
	App.data.BlueEarth.isPresent = false;
}
App.namespace('App.data');

App.data.InteractionStore = {
    _interactions: {},

    add: function (interaction) {
        var id = interaction.id;
        this._interactions[id] = interaction;
    },

    findById: function (interactionId) {
        return this._interactions[interactionId] || null;
    },

    loadActivityData: function (activities) {
        var activity,
            interactionId,
            interaction,
            duration,
            userResponse,
            now = new Date(),
            len = activities.length,
            i;

        for (i = 0; i < len; i++) {
            activity = activities[i];
            interactionId = activity.interaction_id;
            duration = activity.duration * 1000;    // sent to us as seconds; we use milliseconds.
            userResponse = activity.given_answers;
            interaction = this.findById(interactionId);
            if (!interaction) {
                App.Logger.warn('loadActivityData: could not find interaction with id ' + interactionId);
                continue;
            }
            interaction.stopTime = now;
            interaction.startTime = new Date(now - duration);
            interaction.setUserResponse(userResponse);
            interaction.dialog.setFormData(userResponse);
        }
    }
};

window.loadActivityData = function (activityObjectArray) {
    App.data.InteractionStore.loadActivityData(activityObjectArray);
};
App.namespace('App.data');

// TODO rename LocalStorage to Storage
App.data.LocalStorage = {
	/**
	 * Load the user's responses to an interaction from HTML5 LocalStorage.
	 * @param {string} anInteractionId The unique id for the interaction to load (e.g., 'tf.1').
	 * @returns {*}
	 */
	load: function (anInteractionId) {
		var stringValue = localStorage.getItem(anInteractionId);

		return JSON.parse(stringValue);
	},

	/**
	 * Save the user's responses to an interaction to HTML5 LocalStorage.
	 * @param {string} anInteractionId The unique id of the interaction to save (e.g., 'tf.1').
	 * @param {*} aValue The interaction data to save. Can be anything that can be serialized via JSON.stringify().
	 */
	save: function (anInteractionId, aValue) {
		var stringValue = JSON.stringify(aValue);

		localStorage.setItem(anInteractionId, stringValue);
	}
};

// If we're running in the BlueEarth iFrame, turn the methods above into no-ops.
if (App.data.BlueEarth.isPresent) {
    App.data.LocalStorage.load = function () {};
    App.data.LocalStorage.save = function () {};
}
App.namespace('App.data');

/**
 * Dependencies: App.data.BlueEarth, App.data.InteractionStore
 */
App.data.Page = {
	loadPageData: function () {
		var DialogType,
			InteractionType,
			interactionConfig,
			interaction,
			dialog,
			dialogParent,
			i;

		if (typeof pageData === 'undefined' ||
			typeof pageData.interactions === 'undefined' ||
			typeof pageData.interactions.length !== 'number') {
			App.Logger.warn('Could not find interactions[] in window.pageData');
			return;
		}

		// If we're running in an iFrame under the BlueEarth environment, find the body element in the parent window.
		// Otherwise, assume we're running in the top-level window (e.g., in the ePub reader), so get that body element.
		// When we create JQuery UI Dialog objects later, we'll append them to the BODY element that we select here.
		dialogParent = App.data.BlueEarth.isPresent ? window.parent.jQuery('body') : $('body');

		for (i = 0; i < pageData.interactions.length; i++) {
			interactionConfig = pageData.interactions[i];
			switch (interactionConfig.type) {
				case 'text':
					DialogType = App.view.dialog.Note;
					InteractionType = App.data.interaction.static.NoteInteraction;
					break;
				case 'qa':
					DialogType = App.view.dialog.answerable.QA;
					InteractionType = App.data.interaction.answerable.QAInteraction;
					break;
				case 'audio':
					DialogType = App.view.dialog.Audio;
					InteractionType = App.data.interaction.static.AudioInteraction;
					break;
                case 'checklist':
                    DialogType = App.view.dialog.ChecklistDialog;
                    InteractionType = App.data.interaction.static.ChecklistInteraction;
                    break;
				case 'tf':
					DialogType = App.view.dialog.answerable.TrueFalse;
					InteractionType = App.data.interaction.answerable.TrueFalseInteraction;
					break;
				case 'fib':
					DialogType = App.view.dialog.answerable.FillTheBlank;
					InteractionType = App.data.interaction.answerable.multifield.FillTheBlankInteraction;
					break;
				case 'mtc':
					DialogType = App.view.dialog.answerable.MatchTheColumn;
					InteractionType = App.data.interaction.answerable.MatchTheColumnInteraction;
					break;
				case 'mc':
					InteractionType = App.data.interaction.answerable.MultipleChoiceInteraction;
					DialogType = App.view.dialog.answerable.MultipleChoiceDialog;
					break;
                case 'survey':
                    InteractionType = App.data.interaction.static.SurveyInteraction;
                    DialogType = App.view.dialog.SurveyDialog;
                    break;
				case 'wordbank':
					InteractionType = App.data.interaction.answerable.multifield.WordBankInteraction;
					DialogType = App.view.dialog.answerable.WordBankDialog;
					break;
				default:
					App.Logger.log('Unknown interaction type:', interactionConfig.type);
					// Returning here will leave the page only partially configured,
					// but if the data is corrupt, we have bigger problems anyway.
					return;
			}
			try {
				interaction = App.create(InteractionType, interactionConfig);
				interaction.load();
                App.data.InteractionStore.add(interaction);
				dialog = App.create(DialogType, {
					appendTo: dialogParent,
					interaction: interaction
				});
                interaction.dialog = dialog;
			} catch (e) {
				App.Logger.error('Could not instantiate interaction or dialog in Page.js');
				App.Logger.log(e.stack);
				App.Logger.log('Interaction type:', InteractionType);
				App.Logger.log('Dialog type:', DialogType);
				App.Logger.log('Interaction config:', interactionConfig);
				// skip the broken interaction.
				continue;
			}

			// `dialog` is a mutable variable, so we need to capture its current value and
			// create an anonymous function that opens the current dialog when triggered.
			var callback = (function (dialog) {
				return function (event) {
					// In case the trigger is near the edge of the screen, stop the event
					// from its default action of turning the page.
					if (event) { //only if event exists
						event.preventDefault();
					}
					dialog.open();
				}
			}(dialog));

			//TO-DO - Storing reference to anonymous function for external call. Clean up.
			pageData.interactions[i].callback = callback;

			App.create(App.view.trigger.Trigger, {
				interactionConfig: interactionConfig,
				callback: callback
			});
		}
	}
};
App.namespace('App.data.interaction.answerable');

App.data.interaction.answerable.MatchTheColumnInteraction = (function () {
	var parent = App.data.interaction.answerable.AnswerableInteraction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		// Matching-column interactions are always gradable, so this key does not appear in the data.
		interactionConfig.gradable = true;

		parent.init.call(this, interactionConfig);

		this.answerChoices = interactionConfig.answerChoices;
	};

	return exports;
}());
App.namespace('App.data.interaction.answerable');

// TODO refactor this; it's basically the same code as MatchTheColumnInteraction.js
App.data.interaction.answerable.MultipleChoiceInteraction = (function () {
	var parent = App.data.interaction.answerable.AnswerableInteraction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		// Multiple-choice interactions are always gradable, so this key does not appear in the data.
		interactionConfig.gradable = true;

		parent.init.call(this, interactionConfig);

		this.answerChoices = interactionConfig.answerChoices;
	};

	/**
	 * User's menu choices, as menu-item indices, converted to integers and nulls.
	 * @returns {Array<number|null>}
	 */
	exports.getUserResponse = function () {
		var retVal = [],
			response,
			value,
			i;

		for (i = 0; i < this.userResponse.length; i++) {
			response = this.userResponse[i];
            if (typeof response === 'string') {
                if (response.length === 0) {
                    value = null;
                } else {
                    value = parseInt(response);
                }
            } else if (typeof response === 'number') {
                value = response;
            } else {
                value = null;
            }
			retVal.push(value);
		}

		return retVal;
	};

	return exports;
}());
App.namespace('App.data.interaction.answerable');

App.data.interaction.answerable.QAInteraction = (function () {
	var parent = App.data.interaction.answerable.AnswerableInteraction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		// Call parent constructor
		parent.init.call(this, interactionConfig);
	};

	return exports;
}());
App.namespace('App.data.interaction.answerable');

App.data.interaction.answerable.TrueFalseInteraction = (function () {
	var parent = App.data.interaction.answerable.AnswerableInteraction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		// True/False interactions are always gradable, so this key does not appear in the data.
		interactionConfig.gradable = true;
		parent.init.call(this, interactionConfig);

		this.trueLabel = interactionConfig.trueLabel ? interactionConfig.trueLabel : 'True';
		this.falseLabel = interactionConfig.falseLabel ? interactionConfig.falseLabel : 'False';
		this.blankLabel = interactionConfig.blankLabel ? interactionConfig.blankLabel : '(vide)';
	};

	return exports;
}());
App.data.interaction.answerable.multifield.FillTheBlankInteraction = (function () {
	var parent = App.data.interaction.answerable.multifield.MultifieldAnswerableInteraction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		var questionParts,
			i,
			numQuestions,
			j,
			answerParts,
			numAnswerParts,
			underlinedAnswerParts;

		parent.init.call(this, interactionConfig);

		this.splitQuestions = [];
		this.questions = [];
		this.answers = [];

		// TODO move this view-specific logic in a view class
		for (i = 0, numQuestions = this.originalQuestions.length; i < numQuestions; i++) {
			questionParts = this.originalQuestions[i].split('<blank>');
			this.splitQuestions.push(questionParts);
			// TODO move this dialog-friendly-question-text-replacement code to someplace more obvious:
			this.questions.push(questionParts.join('_____'));
			answerParts = this.originalAnswers[i];
			underlinedAnswerParts = [];
			for (j = 0, numAnswerParts = answerParts.length; j < numAnswerParts; j++) {
				underlinedAnswerParts.push('<u>' + answerParts[j] + '</u>');
			}
			this.answers.push(this.assembleSentence(questionParts, underlinedAnswerParts));
		}
	};

	exports.assembleSentence = function (sentenceParts, fillInTheBlankParts) {
		var numQuestionParts = sentenceParts.length,
			sentencePart,
			blankPart,
			numBlankParts = fillInTheBlankParts.length,
			assembledSentence = '',
			i;

		for (i = 0; i < numQuestionParts; i++) {
			sentencePart = sentenceParts[i];
			blankPart = i < numBlankParts ? fillInTheBlankParts[i] : '';
			assembledSentence += sentencePart + blankPart;
		}

		return assembledSentence;
	};

	exports._processValue = function (value) {
		return App.trim(value).toLowerCase().split('â€™').join('\'');
	};

	exports.checkAnswer = function () {
		return parent.checkAnswer.apply(this, arguments);
	};

	return exports;
}());
App.namespace('App.data.interaction.answerable');

App.data.interaction.answerable.multifield.WordBankInteraction = (function () {
	var parent = App.data.interaction.answerable.multifield.MultifieldAnswerableInteraction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		parent.init.call(this, interactionConfig);

		// word-bank interactions are always gradable
		this.gradable = true;
		this.answerChoices = interactionConfig.answerChoices;
	};

	exports._processValue = function (value) {
		return parseInt(value);
	};

	return exports;
}());
App.namespace('App.data.interaction.static');

App.data.interaction.static.AudioInteraction = (function () {
	var parent = App.data.interaction.Interaction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		// Call parent constructor
		parent.init.call(this, interactionConfig);
	};

	// Override save() and make it a no-op, because Audio interactions have no user input that can be saved.
	exports.save = App.emptyFn;

    exports.computeNumCorrect = function () {
        return 1;
    };

	return exports;
}());
App.namespace('App.data.interaction.static');

App.data.interaction.static.ChecklistInteraction = (function () {
    var parent = App.data.interaction.Interaction,
        exports = Object.create(parent);

    exports.init = function (interactionConfig) {
        parent.init.call(this, interactionConfig);
        this.prompt = interactionConfig.prompt || null;
        this.sections = interactionConfig.sections || [];
        this.userResponse = [];
    };

    exports.load = function () {
        this.userResponse = App.data.LocalStorage.load(this.id) || [];
    };

    exports.computeNumCorrect = function () {
        var userResponse = this.userResponse,
            numSections = userResponse.length,
            items,
            numItems,
            numCorrect = 0,
            i,
            j;

        for (i = 0; i < numSections; i++) {
            items = userResponse[i];
            numItems = items.length;
            for (j = 0; j < numItems; j++) {
                if (items[j].isChecked === true) {
                    numCorrect++;
                }
            }
        }

		this.numCorrect = numCorrect;
    };

    return exports;
}());
// requires App.data.interaction.Interaction, App.data.LocalStorage

App.data.interaction.static.NoteInteraction = (function () {
	var parent = App.data.interaction.Interaction,
		exports = Object.create(parent);

	exports.init = function (interactionConfig) {
		// Call parent constructor
		parent.init.call(this, interactionConfig);

		this.questions = interactionConfig.questions;
		this.userResponse = '';
	};

	exports.load = function () {
		this.userResponse = App.data.LocalStorage.load(this.id) || '';
	};

    exports.computeNumCorrect = function () {
		// Correct if textarea's string is non-empty.
		this.numCorrect = this.userResponse.length > 0 ? 1 : 0;
    };

	return exports;
}());
App.namespace('App.data.interaction.static');

App.data.interaction.static.SurveyInteraction = (function () {
    var parent = App.data.interaction.Interaction,
        exports = Object.create(parent);

    exports.init = function (interactionConfig) {
        parent.init.call(this, interactionConfig);
        this.multiLineInput = interactionConfig.multiLineInput;
        this.sections = interactionConfig.sections || [];
        this.userResponse = [];
    };

    exports.load = function () {
        this.userResponse = App.data.LocalStorage.load(this.id) || [];
    };

	exports.computeNumCorrect = function () {
		var userResponse = this.userResponse,
			numResponses = userResponse.length,
			numCorrect = 0,
			i,
			j;

		for (i = 0; i < numResponses; i++) {
			// Each non-empty string counts as correct.
			if (userResponse[i].length > 0) {
				numCorrect++;
			}
		}

		this.numCorrect = numCorrect;
	};

	return exports;
}());
App.namespace('App.view.dialog');

App.view.dialog.Dialog = {
	audioPath: null,
	form: null,

	/**
	 * A prefix to prepend to the ID of the dialog's DIV element so that we stay out of the parent frame's namespace.
	 * @type string
	 */
	idPrefix: 'page-interaction-',

	interaction: null,
	container: null,

	/**
	 * Initialize the Dialog.
	 * @param {Object} config Configuration object. Required key: 'interaction'
	 * @param {Object} config.appendTo The JQuery object to which to append the new dialog's DIV.
	 * @param {App.data.interaction.Interaction} config.interaction The data object for this view.
	 */
	init: function (config) {
		var me = this,
			$ = App.data.BlueEarth.isPresent ? window.parent.jQuery : window.$,
            pageTurnPreventionFn,
			prompt;

		if (config && config.interaction) {
			this.interaction = config.interaction;
			prompt = config.interaction.prompt;
		} else {
			App.Logger.error('Dialog constructor requires "interaction" parameter in its config object.');
		}

		// Render the container into the DOM.
		this.container = $('<div></div>')
			.attr('id', this.idPrefix + this.interaction.id)
			.addClass('dialog-container')
			.appendTo(config.appendTo);

		this.formContainer = $('<div></div>')
			.attr('id', 'form-container-' + this.interaction.id)
			.addClass('dialog-form-container')
			.appendTo(this.container);

		this.promptContainer = $('<div class="prompt-container"/>').appendTo(this.formContainer);
		if (prompt) {
			this.promptContainer.html(prompt);
		}
		this.inputContainer = $('<div class="input-container"/>').appendTo(this.formContainer);

		this.answerContainer = $('<div></div>')
			.attr('id', 'answer-container-' + this.interaction.id)
			.addClass('dialog-answer-container')
			.appendTo(this.container)
			.hide();

		this.container.dialog({
			autoOpen: false,
			close: function () {
				me.onClose();
			},
			draggable: true,
			modal: true,
			height: 'auto',
			width: 500,
			maxHeight: 800
		});

		// jQuery UI Dialog title
		if (this.interaction.title) {
			this.container.dialog('option', 'title', this.interaction.title.split("<i>").join("").split("</i>").join(""));
		}
		
		if (App.data.BlueEarth.isPresent) {
			this.audioPath = this.interaction.audio ? 'OEBPS/' + this.interaction.audio : null
		}
		else {
			this.audioPath = this.interaction.audio ? '../' + this.interaction.audio : null;
		}

        pageTurnPreventionFn = function (event) {
            // event.stopPropagation() and event.stopImmediatePropagation() have no affect on the page-turning
            // and titlebar-toggling behavior of taps in iBooks.

            /*
             // This seemed like a nice idea. Problem is that preventing default action prevented scrolling the
             // content. Also, because it was only sometimes allowing the default action, it seemed annoyingly
             // inconsistent in that tapping somewhere inside the dialog would sometimes, but not always, toggle
             // the iBooks titlebar and statusbar.
             // I'm leaving the (commented-out) code here as a cautionary tale for future maintainers of this code.

             if (event.target === me.container[0]) {	// ignore taps on div that is background of input elements
             App.Logger.log('container tap detected; prevent');
             event.preventDefault();
             }

             if (event.target.hasAttribute('contenteditable') ||	// if tapped element has contenteditable="true"
             event.target.tagName.indexOf('select') > -1 ||	// ...or if it's a SELECT tag
             event.target.tagName.indexOf('input') > -1 ||	// ...or if it's an INPUT tag
             event.target.tagName.indexOf('label') > -1) {	// ...or if it's a LABEL tag
             // allow default action on interaction widgets.
             App.Logger.log('interactive widget tap; allow');
             } else {											// if it's anything else...
             App.Logger.log('non-interactive widget tap; prevent');
             event.preventDefault();							// ...prevent
             }
             */
        };

        // If parent.FrameInterface is NOT defined, then presumably we are NOT running in the web-based e-reader's
        // iFrame, so we will assume that we are running in iBooks and attaching the handlers that prevent page-turning
        // when a dialog is open.
		// TODO replace this with a check of App.data.BlueEarth.isPresent
        if (typeof parent['FrameInterface'] === 'undefined') {
            // TODO figure out when jquery's .on() isn't doing the right thing. Plain JavaScript works as expected.
            //this.container.on('click touchstart', pageTurnPreventionFn);
            this.container.get(0).addEventListener('click', pageTurnPreventionFn, false);
            this.container.get(0).addEventListener('touchstart', pageTurnPreventionFn, false);
        }
	},

	audioEnable: function () {
		if (this.audioPath) {
			this.formContainer.prepend('<audio controls="controls" src="' + this.audioPath + '">' +
				'Your browser does not support the audio tag.</audio>');
		}
	},

	audioDisable: function () {
		if (this.audioPath) {
			this.container.find('audio').remove()
		}
	},

	open: function () {
		var me = this,
			containerParentBody = this.container.parents('body'),
			dialogMask;

		this.showFormPanel();
		this.audioEnable();
		this.container.dialog('open');

		// TODO move this interaction-timer logic to a service object or something
		this.interaction.startTime = new Date();

		// Now that the dialog is open, the overlay should be present, so we can get a reference to it.
		dialogMask = containerParentBody.find('div.ui-widget-overlay');

		// If the user clicks/taps outside the dialog box, dismiss the dialog.
		// NOTE: this will only work if modal=true for the dialog, because it relies on the mask being present.
		dialogMask.on('click touchstart', function (event) {
			event.preventDefault();
			me.close();
		});
	},

	close: function () {
		this.container.dialog('close');
		// jQuery will now fire a 'close' event, so put any other dialog-close-related logic into the
		// #onClose() event-handler method below to make sure that code also runs if the dialog is closed
		// another way, e.g., with the user tapping the "X" close button in the title-bar.
	},

	showFormPanel: function () {
		this.answerContainer.hide();
		this.formContainer.show();
	},

	showAnswerPanel: function () {
		this.formContainer.hide();
		this.answerContainer.show();
	},

	save: function () {
		var data = this.getFormData();

		// TODO move this interaction-timer logic to a service object or something
		this.interaction.stopTime = new Date();
		this.interaction.setUserResponse(data);
		this.interaction.save();
	},

	setForm: function (jqueryElement) {
		this.form = jqueryElement;
		this.inputContainer.empty();
		jqueryElement.appendTo(this.inputContainer);
		if (this.interaction) {
			this.setFormData(this.interaction.getUserResponse());
		}
	},

	getFormData: function () {
		App.Logger.log('Dialog#getFormData called; should override in subclass.');
		return null;
	},

	setFormData: function (data) {
		App.Logger.log('Dialog#setFormData called; should override in subclass.');
	},

	onClose: function () {
		this.audioDisable();
	}
};
// extends App.view.dialog.Dialog

App.namespace('App.view.dialog.answerable');

App.view.dialog.answerable.Answerable = Object.create(App.view.dialog.Dialog);

App.view.dialog.answerable.Answerable.init = function () {
	var retVal = App.view.dialog.Dialog.init.apply(this, arguments);
    this._addFocusStealingButtonTo(this.container);
    return retVal;
};

App.view.dialog.answerable.Answerable.getDisplayableQuestion = function (questionNumber) {
	return this.interaction.questions[questionNumber];
};

App.view.dialog.answerable.Answerable.getDisplayableResponse = function (responseNumber) {
	var response = this.getFormData()[responseNumber];

	return response === '' || response === null ? '(blank)' : response;
};

App.view.dialog.answerable.Answerable.getDisplayableAnswer = function (answerNumber) {
	var answers = this.interaction.answers;
	return answers[answerNumber];
};

App.view.dialog.answerable.Answerable.checkAnswers = function () {
	var scoreHtml = '',
		newHtml = '',
		userResponses = this.getFormData(),
		questions = this.interaction.questions,
		numQuestions = questions.length,
		answers = this.interaction.answers,
		hasAnswers = answers.length > 0,
		isGradable = this.interaction.gradable,
		question,
		answer,
		response,
		responseToDisplay,
		gradeText,
		isCorrect,
		correctAnswerText,
		numCorrect = 0,
		i;

	if (numQuestions !== userResponses.length) {
		App.Logger.error('Number of questions does not match number of responses!');
	}

	for (i = 0; i < numQuestions; i++) {
		question = this.getDisplayableQuestion(i);
		answer = hasAnswers ? this.getDisplayableAnswer(i) : '';
		response = userResponses[i];
		responseToDisplay = this.getDisplayableResponse(i);
		gradeText = '';
		// TODO move this logic either to the Interaction itself or to some kind of service object.
		isCorrect = this.interaction.checkAnswer(i, response);
		if (isCorrect) {
			numCorrect++;
		}
		correctAnswerText = App.template('Answers will vary.<br/>{answer}', {answer: answer});
		if (hasAnswers) {
			if (isGradable) {
				gradeText = App.template('<span class="{isCorrectCls}">{isCorrectText}</span><br/>', {
					isCorrectCls: isCorrect ? 'correct' : 'incorrect',
					isCorrectText: isCorrect ? 'Correct.' : 'Incorrect.'
				});
			}
			correctAnswerText = App.template('<b>Correct answer:</b> {answer}', {answer: answer});
		}

		newHtml += App.template(
			'<p>' +
			'{question}<br/>' +
			'<b>Your Answer:</b> {response}<br/>' +
			gradeText +
			correctAnswerText +
			'</p>', {
				question: question,
				response: responseToDisplay
			});
	}

	if (this.interaction.gradable) {
		scoreHtml = App.template(
			'<p>You answered {numCorrect} out of {numTotal} correctly.</p>',
			{
				numCorrect: numCorrect,
				numTotal: numQuestions
			}
		);
	}

	// TODO move this logic to wherever scores are computed. See TODO note above.
	this.interaction.numCorrect = numCorrect;
	this.save();

	this.answerContainer.html(scoreHtml + newHtml);
	this.showAnswerPanel();
};

App.view.dialog.answerable.Answerable.showFormPanel = function () {
	var me = this;

	App.view.dialog.Dialog.showFormPanel.call(this);

	this.container.dialog('option', 'buttons', {
		"Check Answers": function () {
			me.checkAnswers();
		}
	});
};

App.view.dialog.answerable.Answerable.showAnswerPanel = function () {
	App.view.dialog.Dialog.showAnswerPanel.call(this);
	this.container.dialog('option', 'buttons', {});
};

App.view.dialog.answerable.Answerable._addFocusStealingButtonTo = function (jqueryElement) {
	// UGLY HACK: Focus-stealing button so that the dialog doesn't open with the select menu already open.
	$('<span class="ui-helper-hidden-accessible"><button type="button"/></span>').prependTo(jqueryElement);
};
// requires App.view.dialog.Dialog

App.view.dialog.Audio = Object.create(App.view.dialog.Dialog);

App.view.dialog.Audio.init = function (config) {
	App.view.dialog.Dialog.init.call(this, config);
};
App.namespace('App.view.dialog');

App.view.dialog.ChecklistDialog = (function () {
    var parent = App.view.dialog.Dialog,
        exports = Object.create(parent);

    exports.init = function (config) {
        var form = $('<div class="checklist"></div>'),
            sections = config.interaction.sections || [],
            sectionConfig,
            numSections = sections.length,
            sectionContainer,
            headerContainer,
            headerText,
            items,
            itemText,
            hasInput,
            isMultiLine,
            inputCls,
            numItems,
            labelContainer,
            label,
            i,
            j;

        parent.init.call(this, config);

        // TODO: this is duplicated in Answerable _addFocusStealingButton()
        $('<span class="ui-helper-hidden-accessible"><button type="button"/></span>').appendTo(form);

        for (i = 0; i < numSections; i++) {
            sectionContainer = $('<div></div>').attr('class', 'section');
            sectionConfig = sections[i];

            // Set up the header, if present.
            headerText = sectionConfig.title;
            if (headerText) {
                headerContainer = $('<div></div>').attr('class', 'header');
                headerContainer.html(headerText);
                headerContainer.appendTo(sectionContainer);
            }

            // Set up the checkboxes for the items, if present.
            items = sectionConfig.items;
            if (items) {
                numItems = items.length;
                for (j = 0; j < numItems; j++) {
                    itemText = items[j].text;
                    hasInput = items[j].input;
                    isMultiLine = items[j].multiLine;
                    labelContainer = $('<div></div>').attr('class', 'input-container').appendTo(sectionContainer);
                    label = $('<label></label>').html(itemText).appendTo(labelContainer);
                    $('<input/>').attr('type', 'checkbox').prependTo(label);
                    if (hasInput) {
                        inputCls = isMultiLine ? 'textarea' : 'textfield';
                        $('<div contenteditable="true"/>').attr('class', inputCls).appendTo(labelContainer);
                    }
                }
            }

            sectionContainer.appendTo(form);
        }

        this.setForm(form);
    };

    exports.getFormData = function () {
        var formData = [],
            sectionData,
            inputData,
            sections = this.container.find('div.section'),
            numSections = sections.length,
            section,
            inputContainers = this.container.find('div.input-container'),
            numInputContainers = inputContainers.length,
            inputContainer,
            $inputContainer,
            checkboxes,
            isChecked,
            textFields,
            textAreas,
            i,
            j;

        for (i = 0; i < numSections; i++) {
            section = sections[i];
            sectionData = [];
            inputContainers = $(section).find('div.input-container');
            numInputContainers = inputContainers.length;
            for (j = 0; j < numInputContainers; j++) {
                inputData = {};
                inputContainer = inputContainers[j];
                $inputContainer = $(inputContainer);

                checkboxes = $inputContainer.find('input[type=checkbox]');
                if (checkboxes.length > 0) {
                    isChecked = $(checkboxes[0]).is(':checked');
                    inputData.isChecked = isChecked;
                }

                textFields = $inputContainer.find('div.textfield');
                if (textFields.length > 0) {
                    inputData.text = $(textFields[0]).html();
                }

                textAreas = $inputContainer.find('div.textarea');
                if (textAreas.length > 0) {
                    inputData.text = $(textAreas[0]).html();
                }
                sectionData.push(inputData);
            }
            formData.push(sectionData);
        }

        /*
        for (i = 0; i < numInputContainers; i++) {
            inputContainer = inputContainers[i];
            $inputContainer = $(inputContainer);
            checkboxes = $inputContainer.find('input[type=checkbox]');
            if (checkboxes.length > 0) {
                isChecked = $(checkboxes[0]).is(':checked');
                console.log('checkbox ', i, ' has value ', isChecked);
            }
            textFields = $inputContainer.find('input[type=text]');
            if (textFields.length > 0) {
                console.log('text input', i, 'has value', $(textFields[0]).val());
            }
        }
        */

        return formData;
    };

    /**
     * @param {Array} data
     */
    exports.setFormData = function (data) {
        var sectionContainers,
            $sectionContainer,
            inputContainers,
            $inputContainer,
            checkboxes,
            textFields,
            textAreas,
            sectionData,
            numSections = data.length,
            inputData,
            numInputs,
            isChecked,
            inputText,
            i,
            j;

        sectionContainers = this.container.find('div.section');
        if (numSections !== sectionContainers.length) {
            App.Logger.warn('ChecklistDialog#setFormData: number of data sections does not match number of' +
                ' dialog sections.');
            return;
        }

        for (i = 0; i < numSections; i++) {
            $sectionContainer = $(sectionContainers[i]);
            inputContainers = $sectionContainer.find('div.input-container');
            sectionData = data[i];
            numInputs = sectionData.length;
            if (numInputs !== inputContainers.length) {
                App.Logger.warn('ChecklistDialog#setFormData: number of inputs does not match number of' +
                    ' input containers.');
                return;
            }
            for (j = 0; j < numInputs; j++) {
                inputData = sectionData[j];
                isChecked = !!inputData.isChecked;
                inputText = inputData.text || '';
                $inputContainer = $(inputContainers[j]);
                checkboxes = $inputContainer.find('input[type=checkbox]');
                if (checkboxes.length > 0) {
                    $(checkboxes[0]).prop('checked', isChecked);
                }
                textFields = $inputContainer.find('div.textfield');
                if (textFields.length > 0) {
                    $(textFields[0]).html(inputText);
                    continue;
                }
                textAreas = $inputContainer.find('div.textarea');
                if (textAreas.length > 0) {
                    $(textAreas[0]).html(inputText);
                }
            }
        }
    };

    exports.showFormPanel = function () {
        var me = this;

        // TODO this code is repeated in Note.js: merge. Maybe have an isSavable config param?
        this.container.dialog('option', 'buttons', {
            'Save': function () {
                me.save();
            }
        });
    };

    return exports;
}());
// requires App.view.dialog.Dialog

App.view.dialog.Note = Object.create(App.view.dialog.Dialog);

App.view.dialog.Note.init = function (config) {
	var form = $('<div class="note"/>');

	App.view.dialog.Dialog.init.call(this, config);

    // also the div.textarea stuff should probably be extracted into its own reusable object/class.
    $('<span class="ui-helper-hidden-accessible"><button type="button"/></span>').prependTo(this.container);

	$('<div class="textarea" contenteditable="true"></div>').appendTo(form);
	this.setForm(form);
};

/**
 * Get the user-response data from the dialog's form.
 * @returns {String}
 */
App.view.dialog.Note.getFormData = function () {
	var textWidget = this.form.find('div.textarea');
	return textWidget.html();
};

/**
 * Set user response data inside the dialog's form.
 * @param {String} data
 */
App.view.dialog.Note.setFormData = function (data) {
	var textWidget = this.form.find('div.textarea');
	textWidget.html(data);
};

App.view.dialog.Note.showFormPanel = function () {
	var me = this;

	// super
	App.view.dialog.Dialog.showFormPanel.call(this);

	this.container.dialog('option', 'buttons', {
		'Save': function () {
			me.save();
		}
	});
};
App.namespace('App.view.dialog');

App.view.dialog.SurveyDialog = (function () {
    var parent = App.view.dialog.Dialog,
        exports = Object.create(parent);

    exports.init = function (config) {
        var form = $('<div class="survey"></div>'),
            isMultiLineInput = config.interaction.multiLineInput,
            sections = config.interaction.sections || [],
            numSections = sections.length,
            sectionContainer,
            sectionConfig,
            prompt,
            promptContainer,
            questions,
            numQuestions,
            question,
            questionContainer,
            i,
            j;

        parent.init.call(this, config);

        // TODO: this is duplicated in Answerable _addFocusStealingButton()
        $('<span class="ui-helper-hidden-accessible"><button type="button"/></span>').appendTo(form);

        this.inputCreationString = '<div class="' + (isMultiLineInput ? 'textarea' : 'textfield') + '" contenteditable="true"/>';
        this.inputQueryString = isMultiLineInput ? 'div.textarea' : 'div.textfield';

        for (i = 0; i < numSections; i++) {
            sectionContainer = $('<div class="section"></div>');
            sectionConfig = sections[i];
            prompt = sectionConfig.prompt || null;
            if (prompt) {
                promptContainer = $('<div class="prompt"></div>');
                promptContainer.html(prompt);
                promptContainer.appendTo(sectionContainer);
            }

            questions = sectionConfig.questions || [];
            numQuestions = questions.length;
            for (j = 0; j < numQuestions; j++) {
                questionContainer = $('<div class="survey-question"></div>');
                questionContainer.html(questions[j]).appendTo(sectionContainer);
                $(this.inputCreationString).appendTo(questionContainer);
            }

            sectionContainer.appendTo(form);
        }

        this.setForm(form);
    };

    /**
     * @returns {Array<string>} An array of the user's answers.
     */
    exports.getFormData = function () {
        var inputFields = this.container.find(this.inputQueryString),
            numFields = inputFields.length,
            inputText,
            formData = [],
            i;

        for (i = 0; i < numFields; i++) {
            inputText = $(inputFields[i]).html();
            formData.push(inputText);
        }

        return formData;
    };

    /**
     * Set the values in the input fields from the given data.
     * @param {Array<string>} data
     */
    exports.setFormData = function (data) {
        var numQuestions = data.length,
            inputFields = this.container.find(this.inputQueryString),
            i;

        if (numQuestions !== inputFields.length) {
            App.Logger.warn('SurveyDialog#setFormData: length of "data" does not match')
        }

        for (i = 0; i < numQuestions; i++) {
            $(inputFields[i]).html(data[i]);
        }
    };

    exports.showFormPanel = function () {
        var me = this;

        // TODO this code is repeated in Note.js: merge. Maybe have an isSavable config param?
        this.container.dialog('option', 'buttons', {
            'Save': function () {
                me.save();
            }
        });
    };

    return exports;
}());
// extends App.view.dialog.answerable.Answerable

App.view.dialog.answerable.FillTheBlank = Object.create(App.view.dialog.answerable.Answerable);

App.view.dialog.answerable.FillTheBlank.init = function (config) {
	var form = $('<div class="fib"></div>'),
		questions,
		question,
		numQuestions,
		questionParts,
		answers,
		answerParts,
		i,
		p;

	App.view.dialog.answerable.Answerable.init.call(this, config);

	questions = this.interaction.questions;
	answers = this.interaction.answers;
	numQuestions = questions.length;

	for (i = 0; i < numQuestions; i++) {
		question = questions[i];
		answerParts = answers[i];
		questionParts = this.interaction.splitQuestions[i];
		p = $('<p></p>')
			.attr('class', 'fib')
			.html(
				questionParts.join('<div class="fill-in-the-blank" contenteditable="true"></div>')
			);
		p.appendTo(form);
	}

	this.setForm(form);
};

App.view.dialog.answerable.FillTheBlank.getFormData = function () {
	var responses = [],
		responseGroups = this.form.find('p.fib');

	responseGroups.each(function (index, domElement) {
		var blanks = $(domElement).find('.fill-in-the-blank'),
			blankValues = [];

		blanks.each(function (index, domElement) {
			var responseValue = $(domElement).html();
			blankValues.push(responseValue);
		});

		responses.push(blankValues);
	});

	return responses;
};

App.view.dialog.answerable.FillTheBlank.setFormData = function (data) {
	var responseGroups = this.form.find('p.fib');

	responseGroups.each(function (index, domElement) {
		var blankFields = $(domElement).find('div.fill-in-the-blank'),
			thisResponseData;

		if (index >= data.length) {
			App.Logger.warn('FillTheBlank#setFormData: found more questions than user-response data');
			return;
		}

		thisResponseData = data[index];
		blankFields.each(function (index, domElement) {
			var blankValue;

			if (index >= thisResponseData.length) {
				App.Logger.warn('FillTheBlank#setFormData: found more blanks than values');
				return;
			}

			blankValue = thisResponseData[index];
			$(domElement).html(blankValue);
		})
	});
};

App.view.dialog.answerable.FillTheBlank.getDisplayableResponse = function (responseNumber) {
	var responseParts = this.getFormData()[responseNumber],
		numResponseParts = responseParts.length,
		responsePart,
		formattedResponsePart,
		underlinedResponseParts = [],
		i;

	for (i = 0; i < numResponseParts; i++) {
		responsePart = responseParts[i] === '' ? '_____' : responseParts[i];
		formattedResponsePart = App.template('<u>{responsePart}</u>', {responsePart: responsePart});
		underlinedResponseParts.push(formattedResponsePart);
	}
	return this.interaction.assembleSentence(this.interaction.splitQuestions[responseNumber], underlinedResponseParts);
};
App.namespace('App.view.dialog.answerable');

App.view.dialog.answerable.MatchTheColumn = (function () {
	var parent = App.view.dialog.answerable.Answerable,	// "superclass"
		exports = Object.create(parent);

	/**
	 * Create a jQuery-wrapped HTML SELECT tag with options as a single letter in alphabetical order,
	 * so if numChoices=5, you'll see options A, B, C, D, E.
	 * Note that the letters are only the view on the data; actual values remain the integer answer values 0..N
	 * @private
	 * @param {number} numChoices The number of OPTION menu items to add to the SELECT.
	 * @returns {*|jQuery|HTMLElement} JQuery-wrapped HTML SELECT with numChoices OPTION elements.
	 */
	function createSelect(numChoices) {
		var select = $('<select></select>'),
			optionChar,
			optionElement,
			i;

		// Start with a blank option
		$('<option value="-1"></option>').appendTo(select);

		for (i = 0; i < numChoices; i++) {
			optionChar = String.fromCharCode(i + 65);
			optionElement = $(App.template('<option value="{value}">{char}</option>', {
				value: i,
				char: optionChar
			}));
			optionElement.appendTo(select);
		}

		return select;
	}

	/**
	 * Turn an answer number like 0 into a human-readable string like 'A (hello world)'.
	 * @param {number} answerNumber
	 * @returns {string}
	 * @private
	 */
	function _answerNumberToString(answerNumber) {
		var answerText = this.interaction.answerChoices[answerNumber];

		return App.template('{letter} ({answerText})', {
			letter: String.fromCharCode(answerNumber + 65),
			answerText: answerText
		});
	}

	exports.init = function (config) {
		// Questions will be rendered into one table and answers into another table. Each of those tables will be
		// rendered into a third table, with the questions table in the left column and the answers table in the
		// right column.
		var form = $('<div class="mtc"></div>'),
			mainTable = $('<table class="mtc-container"></table>'),	// not attached to anything yet
			mainTableRow = $('<tr></tr>').appendTo(mainTable),
			mainTableRowLeftColumn = $('<td class="question-table-container"></td>').appendTo(mainTableRow),
			mainTableRowRightColumn = $('<td class="answer-table-container"></td>').appendTo(mainTableRow),
			questionTable = $('<table class="question-table"></table>').appendTo(mainTableRowLeftColumn),
			answerTable = $('<table class="answer-table"></table>').appendTo(mainTableRowRightColumn),
			questionTableRow,
			selectColumn,
			select,
			questionText,
			questionColumn,
			answerTableRow,
			answerLabelColumn,
			answerText,
			numChoices,
            numResponses,
			i;

		parent.init.call(this, config);

		this.container.dialog('option', 'width', 660);

		numChoices = config.interaction.questions.length;
		numResponses = config.interaction.answerChoices.length;

		// Build the contents of the question table.
		for (i = 0 ; i < numChoices; i++) {
			questionTableRow = $('<tr></tr>').appendTo(questionTable);

			questionText = config.interaction.questions[i];
			questionColumn = $('<td></td>').html(questionText).appendTo(questionTableRow);

			selectColumn = $('<td></td>').appendTo(questionTableRow);
			select = createSelect(numResponses);
			select.appendTo(selectColumn);
		}

		// Build the contents of the answer table
		for (i = 0; i < numResponses; i++) {
			answerTableRow = $('<tr></tr>').appendTo(answerTable);
			answerLabelColumn = $('<td></td>')
				.text(String.fromCharCode(i + 65))
				.appendTo(answerTableRow);
			answerText = config.interaction.answerChoices[i];
			$('<td></td>').html(answerText).appendTo(answerTableRow);
		}

		mainTable.appendTo(form);

		// Save reference now to avoid DOM searches later.
		this.questionTable = questionTable;

		this.setForm(form);
	};

	/**
	 * For the given question/answer/response number get a string version of the response suitable for display
	 * on the Answer screen.
	 * @param {number} responseNumber
	 * @returns {string}
	 */
	exports.getDisplayableResponse = function (responseNumber) {
		var response = this.getFormData()[responseNumber];

		if (response === -1) {
			return '(blank)';
		}

		return _answerNumberToString.call(this, response);
	};

	exports.getDisplayableAnswer = function (answerNumber) {
		var answer = this.interaction.answers[answerNumber];

		return _answerNumberToString.call(this, answer);
	};


	/**
	 * Get the selected OPTION value from each SELECT in the form.
	 * @returns {Array<number>} Array of integer values corresponding to the selected options (A=0, B=1, ...)
	 */
	exports.getFormData = function () {
		var selectElements = this.questionTable.find('select'),
			len = selectElements.length,
			answers = [],
			i;

		selectElements.each(function (index, element) {
			var value = parseInt($(element).val());
			answers.push(value);
		});

		return answers;
	};

	/**
	 * Set new values in the form.
	 * @param {Array<number>} newValues Array of integer values corresponding to the selected options (A=0, B=1, ...)
	 */
	exports.setFormData = function (newValues) {
		var selectElements = this.questionTable.find('select'),
			numSelectElements = selectElements.length,
			numNewValues = newValues.length,
			i;

		for (i = 0; i < numNewValues && i < numSelectElements; i++) {
			$(selectElements[i]).val(newValues[i]);
		}
	};

	return exports;
}());
App.namespace('App.view.dialog.answerable');

App.view.dialog.answerable.MultipleChoiceDialog = (function () {
	var parent = App.view.dialog.answerable.Answerable,
		exports = Object.create(parent);

	/**
	 * Constructor
	 * @param {Object} config
	 * @param {App.data.interaction.answerable.MultipleChoiceInteraction} config.interaction
	 */
	exports.init = function (config) {
		var form = $('<div class="multiple-choice"></div>'),
			questions = config.interaction.questions,
			numQuestions = questions.length,
			allAnswerChoices = config.interaction.answerChoices,
			answerChoices,
			numAnswerChoices,
			questionHtml,
			i,
			j;

		parent.init.call(this, config);

		for (i = 0; i < numQuestions; i++) {
			questionHtml = questions[i] + '<br/>';
			answerChoices = allAnswerChoices[i];
			numAnswerChoices = answerChoices.length;
			for (j = 0; j < numAnswerChoices; j++) {
				questionHtml += App.template(
					'<label><input type="radio" name="{name}" value="{index}" />{letter}.) {answerChoice}</label><br/>', {
						name: (this.interaction.id + '-' + i).replace('.', '-'),
						index: j,
						letter: String.fromCharCode(65 + j),
						answerChoice: answerChoices[j]
					}
				);
			}

			$('<p></p>').html(questionHtml).appendTo(form);
		}

		this.setForm(form);
	};

	/**
	 * Get user's response from the Q&A form.
	 * @returns {Array<number|null>} Each element is the index of the selected option for the question at that array
	 * position, or null if no option is selected for that question.
	 */
	exports.getFormData = function () {
		var pTags = this.form.find('p'),
			selectedValue,
			retVal = [],
			i;

		for (i = 0; i < pTags.length; i++) {
			selectedValue = $(pTags[i]).find('input:checked').attr('value');
			selectedValue = typeof selectedValue === 'undefined' ? null : parseInt(selectedValue);
			retVal.push(selectedValue);
		}

		return retVal;
	};

	/**
	 * Set the input fields to match the given data.
	 * @param {Array<number>} data Array of selections to set. Each element is the index/value of the selection.
	 */
	exports.setFormData = function (data) {
		var pTags = this.form.find('p'),
			pTag,
			currentQuestionAnswerValue,
			i;

		if (!data || !data.length) {
			return;
		}

		for (i = 0; i < data.length; i++) {
			pTag = $(pTags.get(i));
			currentQuestionAnswerValue = data[i];
			pTag.find('input').each(function (index, element) {
				$(element).prop('checked', index === currentQuestionAnswerValue);
			});
		}
	};

	exports.getDisplayableResponse = function (responseNumber) {
		var response = this.getFormData()[responseNumber],
			answerChoiceText;

		if (response === null) {
			return '(blank)';
		}

		answerChoiceText = this.interaction.answerChoices[responseNumber][response];

		return App.template('{letter}.) {text}', {
			letter: String.fromCharCode(65 + response),
			text: answerChoiceText
		});
	};

	exports.getDisplayableAnswer = function (questionNumber) {
		var correctAnswerIndex = this.interaction.answers[questionNumber],
			correctAnswerText = this.interaction.answerChoices[questionNumber][correctAnswerIndex];

		return App.template('{letter}.) {text}', {
			letter: String.fromCharCode(65 + correctAnswerIndex),
			text: correctAnswerText
		});
	};

	return exports;
}());
// requires App.view.dialog.Dialog

App.view.dialog.answerable.QA = Object.create(App.view.dialog.answerable.Answerable);

App.view.dialog.answerable.QA.init = function (config) {
	var form = $('<div class="qa"></div>'),
		len,
		i,
		p;

	App.view.dialog.answerable.Answerable.init.call(this, config);

	for (i = 0, len = this.interaction.questions.length; i < len; i++) {
		p = $('<p></p>').html(this.interaction.questions[i]);
		// Add an editable pseudo-textField to the paragraph:
		$('<div class="textfield" contenteditable="true"></div>').appendTo(p);
		p.appendTo(form);
	}

	this.setForm(form);
};

/**
 * Get the values from all the textfields (blanks) in the Q-and-A form.
 * @returns {Array}
 */
App.view.dialog.answerable.QA.getFormData = function () {
	var textFields = this.form.find('div.textfield'),
		numFields = textFields.length,
		fieldValues = [],
		field,
		i;

	for (i = 0; i < numFields; i++) {
		field = $(textFields[i]);
		fieldValues.push(field.html());
	}
	return fieldValues;
};

/**
 * Set the values in all the textfields in the form.
 * @param {Array} data
 */
App.view.dialog.answerable.QA.setFormData = function (data) {
	var textFields = this.form.find('div.textfield'),
		i;

	for (i = 0; i < data.length; i++) {
		$(textFields[i]).html(data[i]);
	}
};
// extends App.view.dialog.Dialog

App.view.dialog.answerable.TrueFalse = Object.create(App.view.dialog.answerable.Answerable);

/**
 * Constructor
 * @param {Object} config
 * @param {App.data.interaction.answerable.TrueFalseInteraction} config.interaction
 */
App.view.dialog.answerable.TrueFalse.init = function (config) {
	var formContainer = $('<div class="tf"></div>'),
		form = $('<form></form>'),
		name,
		radioButtonGroupHtml,
		len,
		i,
		p;

	App.view.dialog.answerable.Answerable.init.call(this, config);

	this.trueLabel = this.interaction.trueLabel;
	this.falseLabel = this.interaction.falseLabel;
	this.blankLabel = this.interaction.blankLabel;
	this.names = [];

	form.appendTo(formContainer);

	for (i = 0, len = this.interaction.questions.length; i < len; i++) {
		name = (this.interaction.id + '-' + i).replace('.', '-');

		this.names.push(name);

		radioButtonGroupHtml = App.template(
			this.interaction.questions[i] +
			'<br/>' +
			'<div class="buttonset" id="{name}">' +
			'<label><input type="radio" name="{name}" value="true" />{trueLabel}</label>' +
			'<label><input type="radio" name="{name}" value="false" />{falseLabel}</label>' +
			'</div>',
			{
				name: name,
				trueLabel: this.trueLabel,
				falseLabel: this.falseLabel
			}
		);
		p = $('<p></p>').html(radioButtonGroupHtml);
		p.appendTo(form);
		// TODO figure out why this is not working
//		p.find('#' + name).buttonset();
	}

	this.setForm(formContainer);
};

/**
 * Get form data.
 * @returns {Array<boolean|null>}
 */
App.view.dialog.answerable.TrueFalse.getFormData = function () {
	var fieldValues = [],
		value,
		nullableBooleanValue,
		name,
		i,
		len;

	for (i = 0, len = this.names.length; i < len; i++) {
		name = this.names[i];

		// TODO replace this with a check on this.isPresent
        if (typeof parent.FrameInterface !== 'object' || typeof parent.FrameInterface.setActivityData !== 'function') {
            value = $('input:radio[name=' + name + ']:checked').val();
        }
        else { //if running in the iframe, utilize the parent document's dom
        	value = parent.$('input:radio[name=' + name + ']:checked').val(); 
        }
		
		if (value === 'true') {
			nullableBooleanValue = true;
		} else if (value === 'false') {
			nullableBooleanValue = false;
		} else {
			nullableBooleanValue = null;
		}
		fieldValues.push(nullableBooleanValue);
	}

	return fieldValues;
};

/**
 * Set the true/false values in all the radio buttons in the form.
 * @param {Array<boolean>} data
 */
App.view.dialog.answerable.TrueFalse.setFormData = function (data) {
	var name,
		value,
		selector,
		len,
		i;

	for (i = 0, len = data.length; i < len; i++) {
		name = this.names[i];
		value = data[i];
        selector = App.template('input[name={name}]', {name: name});
        // `value` can be null, so the following will handle all three cases:
        // "True" is selected -> value=true item is checked
        // "False" is selected -> value=false item is checked
        // Nothing is selected -> both items are unchecked
        $(selector + '[value="true"]').prop('checked', value === true);
        $(selector + '[value="false"]').prop('checked', value === false);
	}
};

App.view.dialog.answerable.TrueFalse.getDisplayableResponse = function (responseNumber) {
	var response = this.getFormData()[responseNumber];

	if (response === true) {
		return this.trueLabel;
	} else if (response === false) {
		return this.falseLabel;
	} else {
		// should only get here if response === null
		return this.blankLabel;
	}
};

App.view.dialog.answerable.TrueFalse.getDisplayableAnswer = function (answerNumber) {
	var answerValue = this.interaction.answers[answerNumber];
	if (answerValue) {
		return this.trueLabel;
	} else {
		return this.falseLabel;
	}
};
App.namespace('App.view.dialog.answerable');

App.view.dialog.answerable.WordBankDialog = (function () {
	var parent = App.view.dialog.answerable.Answerable,
		exports = Object.create(parent);

	exports.init = function (config) {
		var questions = config.interaction.questions,
			numQuestions = questions.length,
			questionNumber,
			questionParts,
			blankNumber,
			numBlanks,
			options,
			numOptions,
			optionNumber,
			form = $('<div class="word-bank"></div>'),
			pTag,
			pTagInnerHtml,
			selectTagHtml;

		parent.init.call(this, config);

		// for each question, split the question on the "<blank>" string in question parts.
		// in between each question part is a menu.
		// for each menu, look up the list of menu options in the interaction
		for (questionNumber = 0; questionNumber < numQuestions; questionNumber++) {
			pTag = $('<p></p>').appendTo(form);

			questionParts = questions[questionNumber].split('<blank>');
			pTagInnerHtml = questionParts[0];

			// "hello".split('<blank>') ==> ['hello'].length = 1 ==> numBlanks = 0
			// "hello<blank>".split('<blank>') == ['hello', ''].length = 2 ==> numBlanks = 1
			// "hello <blank> world".split('<blank>') ==> ['hello ', ' world'].length = 2 ==> numBlanks = 1
			numBlanks = questionParts.length - 1;

			for (blankNumber = 0; blankNumber < numBlanks; blankNumber++) {
				selectTagHtml = '<select>';

				options = config.interaction.answerChoices[questionNumber][blankNumber];
				numOptions = options.length;

				// Start with a blank option at the top of the select menu:
				selectTagHtml += '<option value="-1"></option>';

				for (optionNumber = 0; optionNumber < numOptions; optionNumber++) {
					selectTagHtml += App.template('<option value="{optionValue}">{optionText}</option>', {
						optionValue: optionNumber,
						optionText: options[optionNumber]
					});
//						'<option>' + options[optionNumber] + '</option>';
				}

				selectTagHtml += '</select>';

				pTagInnerHtml += selectTagHtml;
				pTagInnerHtml += questionParts[blankNumber + 1];
			}

			pTag.html(pTagInnerHtml);
		}

		this.setForm(form);
	};

	exports.getDisplayableQuestion = function (questionNumber) {
		var question = this.interaction.questions[questionNumber];

		return question.replace(/<blank>/g, '_____');
	};

	exports.getFormData = function () {
		var pTags = this.form.find('p'),
			responses = [];

		pTags.each(function (index, domElement) {
			// Each question (in a P tag) has a zero or more blanks/menus
			var selectTags = $(domElement).find('select'),
				currentQuestionResponses = [];

			selectTags.each(function (index, domElement) {
				var selectedValue = $(domElement).val();
				currentQuestionResponses.push(selectedValue);
			});

			responses.push(currentQuestionResponses);
		});

		return responses;
	};

	exports.setFormData = function (data) {
		var pTags = this.form.find('p'),
			numQuestions = pTags.length,
			i;

//		parent.setFormData.call(this, data);
		for (i = 0; i < numQuestions, i < data.length; i++) {
			var pTag = $(pTags.get(i));
			var selectTags = pTag.find('select');
			var numSelectTags = selectTags.length;
			var j;
			for (j = 0; j < numSelectTags; j++) {
				var curSelectTagNewValue = data[i][j];
				$(selectTags.get(j)).val(curSelectTagNewValue);
			}
		}
	};

	// TODO refactor getDisplayableAnswer and getDisplayableResponse to reduce code duplication.
	exports.getDisplayableAnswer = function (answerNumber) {
		var questionParts = this.interaction.questions[answerNumber].split('<blank>'),
			answers = this.interaction.answers[answerNumber],
			answerValue,
			answerText,
			answerParts = [],
			i;

		for (i = 0; i < answers.length; i++) {
			answerValue = this.interaction.answers[answerNumber][i];
			answerText = this.interaction.answerChoices[answerNumber][i][answerValue];
			answerParts.push(App.template('<u>{answerText}</u>', {
				answerText: answerText
			}));
		}

		return this.interaction.assembleSentence(questionParts, answerParts);
	};

	exports.getDisplayableResponse = function (responseNumber) {
		// Example: question[responseNumber] = foo ___ bar ___ baz ___ quux
		var responseParts = this.getFormData()[responseNumber],	// ['0', '1', '2']
			responsePartNumber,
			responsePartValue,
			numResponseParts = responseParts.length,
			questionParts = this.interaction.questions[responseNumber].split('<blank>'),
			// => ["foo ", " bar ", " baz ", " quux"]
			answers = this.interaction.answers[responseNumber],	// [1, 2, 1]
			processedResponseParts = [],
			i;

		for (i = 0; i < numResponseParts; i++) {
			responsePartNumber = parseInt(responseParts[i]);
			responsePartValue = this.interaction.answerChoices[responseNumber][i][responsePartNumber];
			processedResponseParts.push(App.template('<u>{selectedResponseText}</u>', {
				selectedResponseText: responsePartValue || '_____'
			}));
		}

		return this.interaction.assembleSentence(questionParts, processedResponseParts);
	};

	return exports;
}());

App.namespace('App.view.trigger');

App.view.trigger.Trigger = {
	CSS_CLASS: 'interaction-trigger',
	AUDIO_ICON_CLASS: 'audio-icon',
	AUDIO_ICON_SRC: '../Content/icons/headphones.png',
	NOTEBOOK_ICON_CLASS: 'notebook-icon',
	NOTEBOOK_ICON_SRC: '../Content/icons/notebook.png',

	init: function (options) {
		var interaction = options.interactionConfig,
			callback = options.callback;

		this.container = $('<div></div>', {
			'class': this.CSS_CLASS,
			offset: {
				top: interaction.top,
				left: interaction.left
			},
			height: interaction.height,
			width: interaction.width,
			on: {
				mousedown: callback,
				touchstart: callback
			}
		}).appendTo('body');

		if (interaction.addAudioIcon) {
			$('<img/>', {
				'class': this.AUDIO_ICON_CLASS,
				src: this.AUDIO_ICON_SRC
			})
				.appendTo(this.container);
		}

		if (interaction.type !== 'audio') {
			$('<img/>', {
				'class': this.NOTEBOOK_ICON_CLASS,
				src: this.NOTEBOOK_ICON_SRC
			}).appendTo(this.container);
		}
	}
};

// Main.js

$(function () {
	App.launch();
});