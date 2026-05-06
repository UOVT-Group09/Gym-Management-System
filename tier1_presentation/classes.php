html
<div class="row">
    <?php 
    $classes = mysqli_query($conn, "SELECT * FROM classes");
    while($class = mysqli_fetch_assoc($classes)): 
        $cid = $class['class_id'];
        $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM class_enrollments WHERE class_id=$cid AND enroll_status='Enrolled'"))['c'];
    ?>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <h5><?php echo $class['class_name']; ?></h5>
                <p>Capacity: <?php echo $count; ?> / <?php echo $class['capacity']; ?></p>
                <?php if($count >= $class['capacity']): ?>
                    <span class="badge bg-warning text-dark">Waitlist Available</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>
