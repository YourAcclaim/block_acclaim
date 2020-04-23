function xmldb_qtype_myqtype_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    /// Add a new column newcol to the mdl_myqtype_options
    if ($oldversion < 2020042200) {

        // Define table block_acclaim_pending_badges to be created.
        $table = new xmldb_table('block_acclaim_pending_badges');

        // Adding fields to table block_acclaim_pending_badges.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badgetemplateid', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('firstname', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('lastname', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('recipientemail', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('expiration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_acclaim_pending_badges.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_acclaim_pending_badges.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table block_acclaim to be renamed to block_acclaim_courses.
        $table = new xmldb_table('block_acclaim');

        // Launch rename table for block_acclaim_courses.
        $dbman->rename_table($table, 'block_acclaim_courses');

        // Acclaim savepoint reached.
        upgrade_block_savepoint(true, 2020042200, 'acclaim');
    }
    return true;
}