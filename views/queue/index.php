<h1>Overview</h1>

<table class="overview">
    <tr>
        <th>Status</th>
        <th>Count</th>
    </tr>
    <tr>
        <td><a href="/queue/queued">Queued Jobs</a></th>
        <td><?php echo $queued_count ?></td>
    </tr>   
    <tr>
        <td><a href="/queue/working">Working Jobs</a></th>
        <td><?php echo $working_count ?></td>
    </tr>

    <tr>
        <td><a href="/queue/pending">Pending Jobs</a></th>
        <td><?php echo $pending_count ?></td>
    </tr>
    <tr<?php echo $failed_count > 0 ? ' class="failure"' : '' ?>>
        <td><a href="/queue/failed">Failed Jobs</a></th>
        <td><?php echo $failed_count ?></td>
    </tr>
</table>