jQuery(function ($) {
  var dval = jQuery(".lp-metaboxes").find("select").data("value");
  var dname = jQuery(".lp-metaboxes").find("select").attr("name");
  if (dval != "" && dname != "") {
    jQuery("." + dname + "-for-" + dval).css("visibility", "visible");
  }
  jQuery(".lp-metaboxes")
    .find("select[name=field-type]")
    .on("change", function () {
      jQuery(".child-meta").css("visibility", "collapse");
      var val = jQuery(this).val();
      var id = jQuery(this).closest("tr").attr("id");
      jQuery("." + id + "-for-" + val).css("visibility", "visible");
    });

  jQuery(document).on("click", ".lp-metaboxes .check.button", function () {
    if (!jQuery(this).hasClass("active")) {
      var tid = jQuery(this).closest("td");
      tid.find(".single-check input").attr("checked", true);
      jQuery(this).val("Deselect all");
      jQuery(this).addClass("active");
    } else {
      var tid = jQuery(this).closest("td");
      tid.find(".single-check input").attr("checked", false);
      jQuery(this).val("Select all");
      jQuery(this).removeClass("active");
    }
  });

  function initialize() {
    var input = document.getElementById("gAddress");
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, "place_changed", function () {
      var place = autocomplete.getPlace();
      document.getElementById("latitude").value = place.geometry.location.lat();
      document.getElementById("longitude").value =
        place.geometry.location.lng();
      google.maps.event.addDomListener(window, "load", lp_initialize_map());
    });
  }
  if (jQuery("input").is("#gAddress")) {
    setTimeout(function () {
      google.maps.event.addDomListener(window, "load", initialize);
    }, 1000);
  }
  if (jQuery("select").is(".multiple-select-options")) {
    jQuery(".multiple-select-options").select2({});
    jQuery(".multiple-select-options").on("select2:select", function (evt) {
      var element = evt.params.data.element;
      var $element = jQuery(element);
      $element.detach();
      jQuery(this).append($element);
      jQuery(this).trigger("change");
    });
    jQuery(".check-show-hide").change(function () {
      var datashow = jQuery(this).attr("data-show");
      var datahide = jQuery(this).attr("data-hide");
      if (jQuery(this).is(":checked")) {
        jQuery(datashow).fadeIn();
        jQuery(datahide).fadeOut();
      } else {
        jQuery(datashow).fadeOut();
        jQuery(datahide).fadeIn();
      }
    });
  }
  jQuery(".check-show-hide").each(function () {
    jQuery(this).change();
  });
  if (jQuery("input").is("#gallery_images_upload")) {
    var frame;
    var images = script_data.image_ids;
    var selection = loadImages(images);
    jQuery("#gallery_images_upload").on("click", function (e) {
      e.preventDefault();
      var options = {
        title: script_data.label_create,
        state: "gallery-edit",
        frame: "post",
        selection: selection,
      };
      if (frame || selection) {
        options.title = script_data.label_edit;
      }
      frame = wp.media(options).open();
      frame.menu.get("view").unset("cancel");
      frame.menu.get("view").unset("separateCancel");
      frame.menu.get("view").get("gallery-edit").el.innerHTML =
        script_data.label_edit;
      frame.content.get("view").sidebar.unset("gallery");
      overrideGalleryInsert();
      frame.on("toolbar:render:gallery-edit", function () {
        overrideGalleryInsert();
      });
      frame.on("content:render:browse", function (browser) {
        if (!browser) return;
        browser.sidebar.on("ready", function () {
          browser.sidebar.unset("gallery");
        });
        browser.toolbar.on("ready", function () {
          if (browser.toolbar.controller._state == "gallery-library") {
            browser.toolbar.$el.hide();
          }
        });
      });
      frame
        .state()
        .get("library")
        .on("remove", function () {
          var models = frame.state().get("library");
          if (models.length == 0) {
            selection = !1;
            jQuery.post(ajaxurl, {
              ids: "",
              action: "lp_save_images",
              post_id: script_data.post_id,
              nonce: script_data.nonce,
            });
          }
        });

      function overrideGalleryInsert() {
        frame.toolbar.get("view").set({
          insert: {
            style: "primary",
            text: script_data.label_save,
            click: function () {
              var models = frame.state().get("library"),
                ids = "";
              models.each(function (attachment) {
                ids += attachment.id + ",";
              });
              this.el.innerHTML = script_data.label_saving;
              jQuery
                .ajax({
                  type: "POST",
                  url: ajaxurl,
                  data: {
                    ids: ids,
                    action: "lp_save_images",
                    post_id: script_data.post_id,
                    nonce: script_data.nonce,
                  },
                  success: function () {
                    selection = loadImages(ids);
                    jQuery("#gallery_image_ids").val(ids);
                    frame.close();
                  },
                  dataType: "html",
                })
                .done(function (data) {
                  jQuery(".gallery-thumbs").html(data);
                });
            },
          },
        });
      }
    });
  }

  function loadImages(images) {
    if (images) {
      var shortcode = new wp.shortcode({
        tag: "gallery",
        attrs: {
          ids: images,
        },
        type: "single",
      });
      var attachments = wp.media.gallery.attachments(shortcode);
      var selection = new wp.media.model.Selection(attachments.models, {
        props: attachments.props.toJSON(),
        multiple: !0,
      });
      selection.gallery = attachments.gallery;
      selection.more().done(function () {
        selection.props.set({
          query: !1,
        });
        selection.unmirror();
        selection.props.unset("orderby");
      });
      return selection;
    }
    return !1;
  }
});

function browseimage(id) {
  var elementId = id;
  window.original_send_to_editor = window.send_to_editor;
  window.custom_editor = !0;
  window.send_to_editor = function (html) {
    if (elementId != undefined) {
      var class_string;
      var checkPattrn = new RegExp("<a");
      var res = checkPattrn.test(html);
      if (res == !0) {
        class_string = jQuery(html).find("img").attr("class");
      } else {
        html = html.match(/<[^>]+>/) + " ";
        class_string = jQuery(html).attr("class");
      }
      var classes = class_string.split(/\s+/);
      var image_id = 0;
      for (var i = 0; i < classes.length; i++) {
        var source = classes[i].match(/wp-image-([0-9]+)/);
        if (source && source.length > 1) {
          image_id = parseInt(source[1]);
        }
      }
      jQuery("#lp_location_image_id").val(image_id);
      jQuery("#lp_category_banner_id").val(image_id);
      var imgurl = "";
      if (res == !0) {
        imgurl = jQuery(html).find("img").attr("src");
      } else {
        imgurl = jQuery(html).attr("src");
      }
      jQuery('input[name="' + elementId + '"]').val(imgurl);
      jQuery(".img-" + elementId).attr("src", imgurl);
      return;
    } else {
      window.original_send_to_editor(html);
    }
    elementId = undefined;
  };
  wp.media.editor.open();
}
window.original_send_to_editor = window.send_to_editor;
window.custom_editor = !0;
