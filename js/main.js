'use strict';

/**
 * Load initial settings, including branch and desk.
 */
$(document).ready( function () {
    setBranchLabel();
    setDeskLabel();
});

/**
 * Sets html for branch label and clears classes for select control.
 */
function setBranchLabel() {
    $('#branch').html(Cookies.get('branch')).removeClass('form-inline form-group-lg');
}

/**
 * Sets html for desk label and clears classes for select control.
 */
function setDeskLabel() {
    $('#desk').html(Cookies.get('desk')).removeClass('form-inline form-group-lg');
}

/**
 * Clears desk label and prepares desk select control.
 *
 * @todo Set desks variable outside of this function and initialize it at
 *  application startup.
 */
function showDeskSelector() {
    var currentDesk = $('#desk').html();

    var desks = [
        { val : 1, text : "Circulation Desk" },
        { val : 2, text : "Reference Desk"},
        { val : 3, text : "Children's Desk"},
    ];

    $('#desk').empty();
    $('#desk').addClass('form-inline form-group-lg');
    var deskList = $('<select>').appendTo('#desk');
    deskList.addClass('form-control');

    var selectItem;
    $(desks).each(function() {
        selectItem = $('<option>').attr('value', this.val).text(this.text).appendTo(deskList);
        if ( this.text === currentDesk ) {
            console.log( 'Selecting ' + currentDesk );
            selectItem.attr('selected', true);
        }
    });
}

/**
 * Clears branch label and prepares desk select control.
 *
 * @todo Set branches variable outside of this function and initialize it at
 *  application startup.
 */
function showBranchSelector() {
    var currentBranch = $('#branch').html();

    var branches = [
        { val : 1, text : 'Bon Air' },
        { val : 2, text : 'Westport'},
        { val : 3, text : 'Crescent Hill'},
        { val : 4, text : 'Fairdale'},
        { val : 5, text : 'Middletown'}
    ];

    $('#branch').empty();
    $('#branch').addClass('form-inline form-group-lg');
    var branchList = $('<select>').appendTo('#branch');
    branchList.addClass('form-control');

    var selectItem;
    $(branches).each(function() {
        selectItem = $('<option>').attr('value', this.val).text(this.text).appendTo(branchList);
        if (this.text === currentBranch) {
            console.log('Selecting ' + currentBranch);
            selectItem.attr('selected', true);
        }
    });
}

/**
 * Sends statistic data to server for logging.
 */
function logStatistic( type, difficulty ) {
    console.log( 'type: ' + type + '   difficulty: ' + difficulty );
}

/**
 * Triggered by clicking on settings button, initiates switch to settings
 * select mode.
 */
function changeSettings() {
    toggleSettings();
}

/**
 * Switches visibility between desk/branch labels and select controls.
 */
function toggleSettings() {
    if ( $('#settings-buttons').hasClass('hidden') ) {
        showDeskSelector();
        showBranchSelector();
    } else {
        setDeskLabel();
        setBranchLabel();
    }

    $("#open-settings").toggleClass("hidden");
    $("#settings-buttons").toggleClass("hidden");
}

/**
 * Saves settings in desk/branch select controls to browser cookie.
 */
function saveSettings() {
    var desk = $("#desk select option:selected").text();
    var branch= $("#branch select option:selected").text();

    document.cookie = 'desk=' + desk;
    document.cookie = 'branch=' + branch;
    toggleSettings();
    console.log( 'settings saved!');
}

/**
 * Switches out of settings select mode without saving changes.
 */
function cancelSettings() {
    toggleSettings();
    console.log( 'settings NOT saved!' );
}

/**
 * Boop!
 */
function beep() {
    console.log( 'boop!' );
}
