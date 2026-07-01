#!/usr/bin/env bash
set -euo pipefail

plugin_slug="woocommerce-business-id"
plugin_file="${plugin_slug}.php"
dist_dir="dist"
staging_dir="${dist_dir}/${plugin_slug}"

if [[ ! -f "${plugin_file}" ]]; then
	echo "Could not find ${plugin_file}. Run this script from the plugin root." >&2
	exit 1
fi

version="$(
	php -r '
		$contents = file_get_contents( "woocommerce-business-id.php" );
		if ( false === $contents || ! preg_match( "/^[ \t]*\\*[ \t]*Version:[ \t]*(.+)$/m", $contents, $matches ) ) {
			exit( 1 );
		}
		echo trim( $matches[1] );
	'
)"

zip_file="${dist_dir}/${plugin_slug}-${version}.zip"

rm -rf "${staging_dir}" "${zip_file}"
mkdir -p "${staging_dir}/src" "${dist_dir}"

cp "${plugin_file}" "${staging_dir}/"
cp uninstall.php "${staging_dir}/"
cp readme.txt "${staging_dir}/"
cp README.md "${staging_dir}/"
cp LICENSE "${staging_dir}/"
cp -R src/. "${staging_dir}/src/"

(
	cd "${dist_dir}"
	zip -rq "${plugin_slug}-${version}.zip" "${plugin_slug}"
)

rm -rf "${staging_dir}"

echo "${zip_file}"
