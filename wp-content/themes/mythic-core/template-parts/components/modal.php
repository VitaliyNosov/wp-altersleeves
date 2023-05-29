<?php

/**
 * File for generating modals
 *
 * Uses Bootstrap 5 : https://getbootstrap.com/docs/5.0/components/modal/
 *
 * @param string @modal_class - OPTIONAL - Custom classes for the .modal div
 * @param string $id                 - REQUIRED - the modal HTML ID
 *
 * @param string $dialog_claass      - OPTIONAL - Custom classes to the .modal-dialog div
 * @param string $role               - OPTIONAL - the HTML5 role for the modal (default: 'document')
 *
 * @param bool   $close_symbol       - OPTIONAL - True/False to show the close symbol
 * @param bool   $header             - OPTIONAL - True/False to show header section
 * @param string $header_class       - OPTIONAL - Custom classes for the .modal-header div
 * @param string $header_content     - OPTIONAL - Custom content for header
 * @param string $title              - OPTIONAL - The title of the modal
 *
 * @param string $body_class         - OPTIONAL - Custom classes for the .modal-boy div
 * @param string $body_content       - REQUIRED - The content of the modal body
 *
 * @param bool   $close_button       - OPTIONAL - True/False to show the close button in the modal footer
 * @param string $close_button_class - OPTIONAL - Custom classes for the close button in the modal footer
 * @param string $close_button_text  - OPTIONAL - Custom text for close button in the modal footer
 * @param bool   $footer             - OPTIONAL - True/False to show the modal footer section
 * @param string $footer_class       - OPTIONAL - Custom classes for the .modal-footer div
 * @param string $footer_content     - OPTIONAL - Custom content for header
 *
 */

if( empty( $id ) || empty( $body_content ) ) return;

?>

<div class="modal fade <?= $modal_class ?? '' ?>" id="<?= $id ?>" tabindex="-1" role="dialog"
     <?php if( !empty( $title ) ) : ?>aria-labelledby="<?= $id ?>-label"<?php endif ?>
     aria-hidden="true">
    <div class="modal-dialog modal-fullscreen <?= $dialog_class ?? '' ?>" role="<?= $role ?? 'document' ?>">
        <div class="modal-content">
            <?php do_action( 'mc_before_modal_content' ) ?>
            <?php if( !empty( $title ) || !empty( $header ) || !empty( $header_content ) ) : ?>
            <div class="modal-header <?= $header_class ?? '' ?>">
                <?= $header_content ?? '' ?>
                <?php if( !empty( $title ) ) : ?>
                <<?= $title_open_tag ?? 'h5' ?> class="modal-title" id="<?= $id ?>-label">
                    <?= $title ?>
                </<?= $title_close_tag ?? 'h5' ?>>
            <?php endif ?>
                <?php if( !isset( $close_symbol ) || !empty( $close_symbol ) ) : ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <?php endif ?>
            </div>
            <?php endif ?>
            <div class="modal-body <?= $body_class ?? '' ?>">
                <?= $body_content ?? '' ?>
            </div>
            <?php if( !isset( $footer ) || !empty( $footer_content ) ) : ?>
                <div class="modal-footer <?= $footer_class ?? '' ?>">
                    <?= $footer_content ?? '' ?>
                    <?php if( !isset( $close_button ) || !empty( $close_button ) ) : ?>
                        <button type="button" class="btn <?= $close_button_class ?? 'btn-danger' ?>"
                                data-bs-dismiss="modal"><?= $close_button_text ?? 'CLOSE' ?></button>
                    <?php endif ?>
                </div>
            <?php endif ?>
            <?php do_action( 'mc_after_modal_content' ) ?>
        </div>
    </div>
</div>
