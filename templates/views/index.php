<div class="pull-right">
    <a href="<?php echo site_url('resources/new'); ?>" class="btn btn-success">New resource</a>
</div>

<?php foreach($resources as $resource) : ?>

<?php echo $resource['id']; ?>
    <a href="<?php echo site_url('resources/edit/'.$resource['id']); ?>" class="btn btn-info btn-xs">Edit</a>
    <a href="<?php echo site_url('resources/destroy/'.$resource['id']); ?>" class="btn btn-danger btn-xs">Delete</a>

<?php endforeach; ?>

<div class="text-center">
    <?php echo $this->paginator->get_links('resources', 'bootstrap4'); ?>
</div>
