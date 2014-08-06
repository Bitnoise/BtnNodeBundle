$('#tree')// listen for event
    .on('changed.jstree', function (e, data) {
        if (typeof data.node.a_attr.href !== 'undefined' && data.node.a_attr.href !== '#') {
            window.location = data.node.a_attr.href;
        };
    }).jstree();

//init plugin
// $('#nodesManager').nodesTreeManager();

//confirmations
// $('a[data-confirm]').confirmModal();
