#!/bin/sh
set -ve
git checkout cvs
git cvsimport -v -d :pserver:anonymous@cvs.pgfoundry.org:/cvsroot/pgfouine pgfouine
set +v
echo
echo "#"
echo "# proposed actions:"
echo "#"
echo git push
echo git checkout master
echo git merge cvs
