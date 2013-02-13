<?php if ($include_greeting): ?>
Hey <?php echo ucfirst($toUser['Profile']['first_name']); ?>,

<?php endif;?>
<?php echo $content_for_layout;