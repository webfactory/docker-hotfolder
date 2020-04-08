#!/bin/sh -e

[ -z "$UPLOAD_URL" ] && echo "The UPLOAD_URL env var must be set" && exit 1

mkdir -p "$HOTFOLDER" "$ARCHIVE"
[ -z "$HOTFOLDER_OWNER" ] || chown "$HOTFOLDER_OWNER" $HOTFOLDER
[ -z "$HOTFOLDER_PERM" ] || chmod "$HOTFOLDER_PERM" $HOTFOLDER

sh -c 'while true; do sleep $PURGE_INTERVAL; bin/console hotfolder:purge "$ARCHIVE" ; done' &

while true; do
    inotifywait --quiet --timeout "$RESCAN_INTERVAL" --event create,modify --recursive $HOTFOLDER || true 
    bin/console hotfolder:upload --form-field-name=$FORM_FIELD_NAME $UPLOAD_URL $HOTFOLDER "$PATTERN" $ARCHIVE
done
