<?php
$title = "Student | " . $student->name;
include $base_page;
?>
<div class="d-flex justify-content-center">
    <h3>
        <a href="/boards/<?php print $board->id; ?>">
            <?php print $board->name; ?>
        </a>
        / </h3>
    <h3><?php print $student->name; ?></h3>
</div>
<table class="table table-striped">
    <tr>
        <form method="post" action="/grades/create">
            <td class="text-right"><h5>Insert New Grade</h5></td>
            <td>
                <input type="number" min="5" max="10" name="grade"
                       class="form-control">
                <input type="hidden" value="<?php echo $student->id; ?>" name="student_id">
                <input type="hidden" name="token" value="<?php print $token; ?>">
            </td>
            <td><button type="submit" class="btn btn-outline-success">Insert</button></td>
        </form>
    </tr>
    <tr>
        <th class="text-center">Grade</th>
        <th>Added</th>
        <th>Delete</th>
    </tr>
    <?php
    foreach($grades as $grade){
        ?>
        <tr>
            <td class="text-center"><?php print $grade->grade; ?></td>
            <td><?php print date('d.m.Y', strtotime($grade->created_at)); ?></td>
            <td><form action="/grades/destroy" method="post">
                    <input type="hidden" name="student_id" value="<?php echo $student->id; ?>">
                    <input type="hidden" name="grade_id" value="<?php echo $grade->id; ?>">
                    <input type="hidden" name="token" value="<?php print $token; ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php } ?>
    <tr class="font-weight-bold">
        <?php
        if($board->id == 1){
            ?>
            <td class="text-right">Average Grade:</td>
            <td><?php print round($averageGrade, 2); ?></td>
            <td><?php print $averageGrade < 7 ?
                    '<div class="text-danger">Failed</div>' :
                    '<div class="text-success">Passed</div>'; ?>
            </td>
        <?php }else{
            ?>
            <td class="text-right">Highest Grade:</td>
            <td><?php print $maxGrade; ?></td>
            <td>
                <?php
                if(count($grades) > 2 && $maxGrade >= 8){
                    print 'Passed';
                }else{
                    print 'Failed';
                }
                ?>
            </td>
        <?php } ?>
    </tr>
</table>
