<?php
// Builds demo/nathfoods-theme.zip from the theme folder. Run: php demo/build-zip.php
$root = realpath( __DIR__ . '/../site/wp-content/themes/nathfoods' );
$zip  = new ZipArchive();
$zip->open( __DIR__ . '/nathfoods-theme.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE );
$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $file ) {
	$rel = str_replace( DIRECTORY_SEPARATOR, '/', substr( $file->getPathname(), strlen( $root ) + 1 ) );
	$zip->addFile( $file->getPathname(), 'nathfoods/' . $rel );
}
$zip->close();
echo filesize( __DIR__ . '/nathfoods-theme.zip' ), " bytes\n";
