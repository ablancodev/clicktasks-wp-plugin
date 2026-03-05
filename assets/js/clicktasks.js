/**
 * ClickTasks - Entry point
 */
(function () {
    'use strict';

    if (!document.getElementById('ct-app')) return;

    CT.Modal.init();
    CT.Router.init();
    CT.Forms.init();
    CT.Sidebar.init();
    CT.Filters.init();
    CT.TaskDetail.init();
})();
