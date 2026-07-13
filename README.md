# Docker image to watch a folder and HTTP POST uploaded files

This repository contains a very simple Symfony application to HTTP POST all files from
a given directory that have been modified at least 30 seconds ago to a given URL, and to 
move them to an `archive` folder afterwards. And a second command to prune older files
from that archive directory.

Started from a shell script in combination with `inotify`, this can be used to do
monitor a directory exported over NFS, SMB or FTP and forward new files to some API endpoint.

## Running the Published Image

The image is built for the `amd64` and `arm64` platforms. A GitHub Actions workflow builds and
publishes it on every merge to `master`, and once a week regardless of code changes, so the base
PHP image should stay up-to-date automatically.

The resulting image is published at https://ghcr.io/webfactory/hotfolder. To use it:

```
docker run --rm \
    -e UPLOAD_URL=https://example.com/upload \
    -v /path/to/hotfolder:/hotfolder \
    ghcr.io/webfactory/hotfolder
```

Besides `:latest`, every build is also tagged with the commit it was built from
(`:sha-<short-sha>`) and with a unique, immutable tag per build (`:sha-<short-sha>-<run-number>`)
that can be used to pin or roll back to a specific version.

## Credits, Copyright and License

This action was written by webfactory GmbH, Bonn, Germany. We're a software development
agency with a focus on PHP (mostly [Symfony](http://github.com/symfony/symfony)). If you're a
developer looking for new challenges, we'd like to hear from you!

- <https://www.webfactory.de>

Copyright 2019 – 2026 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
