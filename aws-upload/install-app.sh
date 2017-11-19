#!/usr/bin/env bash

npm run build

# get the location of this script
SRC="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# location of template files to modify
template_event_path=${SRC}/../ext/ukrgb/template/styles/ukrgb-prosilver/template/event

cp -a ${SRC}/dist/* ${SRC}/../ext/ukrgb/template/vue-app/

for f in $SRC/dist/static/css/*.css ;do
	file=$(basename $f .css)
	css_id=$(echo $file | sed -e 's/^app\.//')
done
sed -i -e "/\/css\/app\./s/app\.[0-9a-z]*\.css/app.${css_id}.css/" ${template_event_path}/overall_header_head_append.html

for f in $SRC/dist/static/js/*.js ;do
	file=$(basename $f .js)
	prefix=$(echo $file | grep -o "^[a-z]*")
	echo "update: $prefix"
	sed -i -e "/\/js\/${prefix}\./s/${prefix}\.[0-9a-z]*\.js/${file}.js/" ${template_event_path}/posting_layout_include_panel_body.html
done

