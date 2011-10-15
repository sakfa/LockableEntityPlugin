<h1>TestTables List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Name</th>
      <th>Locked by</th>
      <th>Locked to</th>
      <th>Is locked</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($TestTables as $TestTable): ?>
    <tr>
      <td><a href="<?php echo url_for('test/edit?id='.$TestTable->getId()) ?>"><?php echo $TestTable->getId() ?></a></td>
      <td><?php echo $TestTable->getName() ?></td>
      <td><?php echo $TestTable->getLockedBy() ?></td>
      <td><?php echo $TestTable->getLockedTo() ?></td>
      <td><?php echo !$TestTable->isLockableBy($sf_user->getLogin()) ? 'Yes' : 'No'; ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('test/new') ?>">New</a>
