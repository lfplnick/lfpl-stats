'use strict';

function logStatistic( type, difficulty ) {
    console.log( 'type: ' + type + '   difficulty: ' + difficulty );
}

function toggleSettings() {
    $("#open-settings").toggleClass("hidden");
    $("#settings-buttons").toggleClass("hidden");
}

function saveSettings() {
    toggleSettings();
    console.log( 'settings saved!' );
}

function cancelSettings() {
    toggleSettings();
    console.log( 'settings NOT saved!' );
}

function beep() {
    console.log( 'boop!' );
}
