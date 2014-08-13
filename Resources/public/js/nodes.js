$('#tree')// listen for event
    .on('changed.jstree', function (e, data) {
        if (typeof data.node !== 'undefined' && data.node.a_attr.href !== '#') {
            window.location = data.node.a_attr.href;
        };
    })
;

if ($('#btn_nodesbundle_nodetype_title').length) {
    $('#btn_nodesbundle_nodetype_slug').slugify('#btn_nodesbundle_nodetype_title');
};

//init plugin
// $('#nodesManager').nodesTreeManager();

//confirmations
// $('a[data-confirm]').confirmModal();
