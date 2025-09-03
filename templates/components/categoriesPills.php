<?php
    // require_once "classes/Category.php";
    $_tags = $_SESSION['TAGS']; // bunch of category's IDs
    $_tags_exist = (is_array($_tags) && !empty($_tags));
    $_tag_states = array("inactive", "red", "green", "blue");
    $modal_tags = "";
    $list_tags = "";

    if ($_tags_exist) {
        foreach ($_tags as $_id => $_tag) {
            $_state = $_tag_states[$_tag["priority"]];
            if ($_state != "inactive") {
                $modal_tags .= "<li><a id = 'categ_".$_id."' class = 'uk-badge-color-".$_state." uk-badge uk-badge-small-pill uk-badge-shadow'>".$_tag["name"]."</a></li>"; // RED
                $list_tags .= "<li><a id = 'sel_categ_".$_id."' class = 'uk-badge-color-".$_state." uk-badge uk-badge-pill'>".$_tag["name"]."</a></li>";
            }
        }
        unset($_state);
    }

?>


<div id="modal-center" class="uk-flex-top" uk-modal  style="padding-bottom:10%">
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
        <div class="list-inline uk-flex-between uk-margin-vertical-10 uk-margin-medium-bottom">
            <button id = "close" class="uk-modal-close-default" type="button" uk-close></button>
            <span class = "uk-modal-heading">Categories</span>
            <button id = "reset" class="uk-modal-button">RESET</button>
        </div>
        
        <input id = "search_categ" type="text" class = "uk-form-ctg" placeholder="Search category...">
        <span>Find articles that has all selected categories below: </span>
        <div class="uk-padding-top-small">
            <ul id = "badges" class = "uk-badges uk-padding-remove">
                <?=$modal_tags;?>
            </ul>
        </div>
        <div class="list-inline uk-flex-between uk-margin-vertical-10 uk-margin-medium-top">
            <button id = "cancel" class="uk-modal-button">CANCEL</button>
            <button type = "button" id = "apply" class="uk-modal-button uk-modal-close">APPLY</button>
        </div>
    </div>
</div>


<div class="uk-categories">
    <div class = "uk-display-flex uk-flex-center">
        <ul id = "categories" class = "uk-badges uk-flex-center">
            <?=$list_tags;?>
        </ul>
        <a class = "uk-badge uk-badge-pill uk-badge-plus"  uk-toggle="target: #modal-center">+</a> <!-- could be optimized -->
    </div>
</div>

<script>
var lol_states = {"red" : "green", "green": "blue", "blue" : "inactive", "inactive" : "red"};

$(document).ready(function() {
    var firstLoad = true, user_tags = {};
    var len = "uk-badge-color-".length;
    var modified_categ = {};

    var modal = $("#modal-center");

    function change_state(self, old, changeTo) {
        self.removeClass("uk-badge-color-"+old);
        self.addClass("uk-badge-color-"+changeTo);
        return changeTo;
    }

    function load_categories(somewhere) {
        somewhere.find('[id^=categ_]').each(function() {
            var self = $(this);
            var categ_id = self.attr('id').split('_')[1];
            var initial_state, state;

            $.each(self.attr('class').split(' '),
            function(index, value) {
                if (value.indexOf("uk-badge-color-") == 0) {
                    initial_state = value.substr(len);
                    state = initial_state;
                }
            })

            if (firstLoad) {
                user_tags[categ_id] = state;
            }

            self.click(function() {
                state = change_state(self, state, lol_states[state]);
                modified_categ[categ_id] = {state : state, initial : initial_state};
            })

            $("#reset").click(function() { // removes all
                if (state != "inactive") {
                    state = change_state(self, state, 'inactive');
                    modified_categ[categ_id] = {state : "inactive", initial : initial_state};
                }
            })

            $("#cancel").click(function() { // resets only the new
                if (state != initial_state) {
                    state = change_state(self, state, initial_state);
                }
            })
        })
        
        $("#reset")[0].addEventListener('click', reset_button, false);
        $("#cancel")[0].addEventListener('click', cancel_button, false);
        
    }

    // reset removes ALL (previous and new)
    function reset_button() { 

        Object.keys(modified_categ).forEach(function(key) {
            modified_categ[key].state = "inactive";
        }) // could be optimized

        Object.keys(user_tags).forEach(function(key) {
            if (modified_categ[key] == null) {
                // all user's to "inactive"
                modified_categ[key] = {state : "inactive", initial_state : user_tags[key]};
                $("#categ_"+key).each(function() {
                    // all user's classlist change
                    change_state($(this), user_tags[key], "inactive");
                })
            }
        })

    }

    // cancel basically removes all (new) modifications
    function cancel_button() {
        modified_categ = {};
    }

    // The two button are only saved after 'APPLY'
    $("#apply").click(function() {

        // save all modifications in user_tags[id] = state
        //modified_categ[id] = {state = new, initial_state = old} or null
        var isEmpty = true;
        Object.keys(modified_categ).forEach(function(key) {
            isEmpty = false;
            user_tags[key] = modified_categ[key].state;
        })
        
        if (!isEmpty) { // if modified_categ is not empty
            $.post("pipes/save_categories.php", {temp: JSON.stringify(user_tags)}).done(function(data) {
                $("#categories").empty().append(data);
                // save to session
                // append data to principal categ DIV
            });
        }

        modified_categ = {};
    })

    load_categories($(this));
    
    $("#search_categ").keyup(function() {
        $.post("components/get_categories.php", {search: $(this).val(), temp: JSON.stringify(modified_categ)}).done(function(data) {
            var badges = $("#badges");
            badges.empty().append(data);
            load_categories(badges);
        });
    })
});

</script>