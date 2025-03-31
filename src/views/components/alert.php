<?php
if (!danupe()->data()->get($data, 'icon')) {
    if (danupe()->data()->get($data, 'type') == 'error') {
        $icon = 'fa-solid fa-circle-exclamation';
    }
    if (danupe()->data()->get($data, 'type') == 'info') {
        $icon = 'fa-solid fa-circle-info';
    }
    if (danupe()->data()->get($data, 'type') == 'success') {
        $icon = 'fa-solid fa-circle-check';
    }
    if (danupe()->data()->get($data, 'type') == 'warning') {
        $icon = 'fa-solid fa-triangle-exclamation';
    }
} else {
    if (!preg_match('/fa\-solid/', danupe()->data()->get($data, 'icon'))) {
        $icon = 'fa-solid ' . danupe()->data()->get($data, 'icon');
    } else {
        $icon = danupe()->data()->get($data, 'icon');
    }
}
?>
<div class="alert alert-<?php echo danupe()->data()->get($data, 'type'); ?> mt-4">
    <i class="<?php echo $icon; ?> text-2xl"></i>
    <div class="flex flex-col">
        <span><?php echo danupe()->data()->get($data, 'title'); ?></span>
        <span class="text-content2"><?php echo danupe()->data()->get($data, 'text'); ?></span>
    </div>
</div>