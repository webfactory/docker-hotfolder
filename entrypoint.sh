#!/bin/sh -e

[ -z "$UPLOAD_URL" ] && echo "The UPLOAD_URL env var must be set" && exit 1

sh -c 'while true; do sleep $PURGE_INTERVAL; bin/console hotfolder:purge "$ARCHIVE" ; done' &

[ -z "$HOTFOLDER_OWNER" ] || chown "$HOTFOLDER_OWNER" $HOTFOLDER
[ -z "$HOTFOLDER_PERM" ] || chmod "$HOTFOLDER_PERM" $HOTFOLDER

while true; do
    inotifywait --quiet --timeout 10 --event create,modify --recursive $HOTFOLDER || true 
    bin/console hotfolder:upload --form-field-name=$FORM_FIELD_NAME $UPLOAD_URL $HOTFOLDER "$PATTERN" $ARCHIVE
done
