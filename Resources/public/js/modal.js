(function ($) {

  if(typeof window.btnNode === 'object') {
    return false;
  }

  $(document).ready(function(){
    var modalEl          = null;
    var modalElWrap      = null;
    var button           = '<div class="btn btn-primary">Choose site</div>';
    var deleteButton     = '<div class="btn btn-danger" data-confirm="Are you sure you want to delete?" style="margin:0 0 0 5px;">Delete</div>';
    var openedFrom       = null;
    // var paginationUrl = '';
    var modalUrl         = $('script[data-node-remote-url]').attr('data-node-remote-url');
    var nodeSelects      = $('select.btn-node');

    if(typeof modalUrl === 'undefined') {
      console.log('No modal url specified');
      return;
    }

    var valueClear = function(value) {
        return $.trim(value.replace('_', ' '));
    };

    var updateButton = function(el, select) {
      $(el).text(valueClear($(select).find('option:selected').text()));
    };

    var resetButton = function(el) {
      $(el).text('Choose site');
    };

    // var updateModalBody = function(url) {
    //   $.get(url, function(response){
    //     modalEl.find('.modal-body').fadeOut(function(){
    //       $(this).html(response).fadeIn(function(){
    //         bindModalBehaviors();
    //       });
    //     });
    //   });
    // };

    var bindModalNavigation = function() {
      // bindPagination();
      // bindCategoryFilter();
    };

    var callback = function(id) {
      var select  = $(openedFrom).prev('select.btn-node');

      select.val(id);
      updateButton(openedFrom, select);
    };

    var bindModalBehaviors = function() {
      modalEl.find('.modal-body style, .modal-body link').appendTo($('head'));

      // paginationUrl = modalEl.find('#bitnoise-media-list').attr('data-pagination-url');

      modalEl.modal({
          show  : false,
          keyboard: true,
          backdrop: (!modalElWrap.hasClass('expanded'))
        });

       modalEl.find('#nodesManager .node-select-link').click(function(event){
        event.preventDefault();
         $('#nodesManager .node-select-link').removeClass('selected');
         $(this).addClass('selected');
       });

      modalEl.find('.submit').click(function(){
        var selected = $('#nodesManager .node-select-link.selected');

        if(selected.length > 0) {
          var id = selected.parent().attr('data-id');

          if(typeof openedFrom.callback !== 'undefined') {
            openedFrom.callback(id);
          }
          else {
            callback(id);
          }

          openedFrom.delButton.show();
          modalEl.modal('hide');
        }
      });

      $(document).on('hidden', '.modal', function () {
          $(this).parent().remove();
      });

      bindModalNavigation();
    };

    var getModal = function() {
      $.get(modalUrl, function(response){
        modalElWrap = $(response);

        modalEl   = modalElWrap.find('.modal');
        modalEl.show();
      });
    };

    var openModal = function() {
      bindModalBehaviors();

      modalElWrap.appendTo($('body'));
      modalEl.modal('show');

      $('html, body').animate({
            scrollTop: modalEl.offset().top
        }, 400);
    };

    // var bindNodeModal = function(el, callback) {
    //   console.log('bindNodeModal');
    //   getModal();

    //   el.click(openModal);

    //   openedFrom      = el;
    //   openedFrom.callback = callback;
    // };

    // window.btnNode = {
    //   bind : bindNodeModal
    // };

    var setDeleteButton = function() {
      var newEl = $(deleteButton);

      //hide if image wasn't choose
      if (nodeSelects.val() == '') {
        newEl.hide();
      }
      //bind reset hidden select and button text
      newEl.click(function(){
        nodeSelects.val(null);
        if (openedFrom == null) {
          resetButton(newEl.prev());
        } else {
          resetButton(openedFrom);
        }
        $(this).hide();
      });

      return newEl;
    };

    getModal();

    $.each(nodeSelects, function(key, el){
        $(el).hide();

        var newEl = $(button);
        var delButton = setDeleteButton();

        newEl.delButton = delButton;

        updateButton(newEl, el);

        newEl.click(function(){
          openedFrom = newEl;
          openModal();
        });

        newEl.insertAfter(el);
        delButton.insertAfter(newEl);
    });

  });//document.ready

})(jQuery);
