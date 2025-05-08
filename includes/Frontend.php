<?php

//Preventing direct access to this file
if (!defined('ABSPATH')) exit;

class Frontend {

    private function get_notes() {
        $user_id = get_current_user_id();
        return json_decode(get_user_meta($user_id, '_user_todo_list', true), true);
    }

    private function get_specific_note($note_index){
        $notes = $this->get_notes();
        if($notes[$note_index]){
            return $notes[$note_index];
        }
    }

    public function save_note(){
        if(!isset($_POST['note_title'], $_POST['note_content'])){
            return false;
        }

        $note_index = $_POST['note_index'] ?? 0;

        $user_id = get_current_user_id();
        $notes = $this->get_notes();

        $notes[$note_index]['title'] = sanitize_text_field($_POST['note_title']);
        $notes[$note_index]['content'] = sanitize_text_field($_POST['note_content']);

        update_user_meta($user_id, '_user_todo_list', json_encode($notes));
    }

    public function delete_note(){
        if(!isset($_POST['note_index'])){
            return false;
        }

        $user_id = get_current_user_id();
        $notes = $this->get_notes();

        unset($notes[$_POST['note_index']]);
        $notes = array_values($notes);

        update_user_meta($user_id, '_user_todo_list', json_encode($notes));
    }

    private function get_next_index(){
        $notes = $this->get_notes();
        if($notes){
            return count($notes);
        }else{
            return 0;
        }
    }

    public function edit_note(){
        if(isset($_POST['note_index'])){
            $note_index = $_POST['note_index'];
            $note = $this->get_specific_note($note_index);
        }else{
            $note_index = $this->get_next_index();
        }
        ob_start();?>
        <div class="todolist__close">
            <img class="todolist__close-image"
                 src="<?php echo plugin_dir_url( __DIR__ ) . 'assets/images/plus.svg'; ?>"
                 alt="Close Todo List">
        </div>
        <div class="todolist__item" data-index="<?php echo $note_index; ?>">
            <div class="todolist__item-title">
                <input type="text" placeholder="Name the To-Do" value="<?php if(isset($note)){echo esc_attr($note['title']);} ?>">
            </div>
            <div class="todolist__item-content">
                <textarea placeholder="Enter A To-Do" rows="5"><?php if(isset($note)){echo esc_textarea($note['content']);} ?></textarea>
            </div>
        </div>
        <div class="todolist__controls">
            <span class="todolist__controls-save todolist__controls-button">
                <?php _e('Save','todolist'); ?>
            </span>
            <span class="todolist__controls-remove todolist__controls-button">
                <?php _e('Remove','todolist'); ?>
            </span>
        </div>
        <?php echo ob_get_clean();
        wp_die();
    }

    public function list_notes() {
        $notes = $this->get_notes();
        ob_start();?>
        <div class="todolist__close">
            <img class="todolist__close-image"
                 src="<?php echo plugin_dir_url(__DIR__) . 'assets/images/plus.svg'; ?>"
                 alt="Close Todo List">
        </div>
        <div class="todolist__items">
            <?php if ($notes):
                foreach ($notes as $index => $note): ?>
                    <div class="todolist__note" data-index="<?php echo $index; ?>">
                        <div class="todolist__note-title">
                            <h4><?php echo esc_html($note['title']); ?></h4>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <h4><?php _e('No Notes Found:(', 'todolist'); ?></h4>
            <?php endif; ?>
        </div>
        <div class="todolist__controls">
            <span class="todolist__controls-add todolist__controls-button">
                <?php _e('Add', 'todolist'); ?>
            </span>
        </div>
        <?php echo ob_get_clean();
        wp_die();
    }

    public function render() {
        if(is_user_logged_in()) {
            ob_start();?>
            <div class="todolist__overlay">
                <div class="todolist__wrapper">

                </div>
            </div>
            <div class="todolist__toggle">
                <img class="todolist__toggle-image"
                     src="<?php echo plugin_dir_url( __DIR__ ) . 'assets/images/plus.svg'; ?>"
                     alt="Show Note Lists"
                >
            </div>
            <?php ob_end_flush();
        }
    }

    public function __construct() {
        add_action('wp_head', [$this, 'render']);

        //Action for showing all notes
        add_action('wp_ajax_list_notes', [$this, 'list_notes']);
        add_action('wp_ajax_nopriv_list_notes', [$this, 'list_notes']);

        //Action for removing note
        add_action('wp_ajax_delete_note', [$this, 'delete_note']);
        add_action('wp_ajax_nopriv_delete_note', [$this, 'delete_note']);

        //Action for adding or saving note
        add_action('wp_ajax_save_note', [$this, 'save_note']);
        add_action('wp_ajax_nopriv_save_note', [$this, 'save_note']);

        //Action for showing specific note
        add_action('wp_ajax_edit_note', [$this, 'edit_note']);
        add_action('wp_ajax_nopriv_edit_note', [$this, 'edit_note']);
    }
}