Summary: pgFouine PostgreSQL log analyzer
Name: pgfouine
Version: 0.6.1
Release: 1
BuildArch: noarch
License: GPL
Group: Development/Tools
Source0: %{name}-%{version}.tar.gz
URL: http://pgfouine.projects.postgresql.org
BuildRoot: %{_tmppath}/%{name}-%{version}-root

AutoReqProv: off
Requires: /usr/bin/php

Patch1: pgfouine-0.1-include_path.patch

%description
pgFouine is a PostgreSQL log analyzer. It generates text or HTML reports
from PostgreSQL log files. These reports contains the list of the slowest queries,
the queries that take the most time and so on.

%prep
%setup
%patch1 -p0

%build

%install
# cleaning build environment
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT

# creating required directories
install -m 755 -d $RPM_BUILD_ROOT/%{_libdir}/%{name}
install -m 755 -d $RPM_BUILD_ROOT/%{_bindir}

# installing pgFouine
for i in include tests ; do
	cp -rp $i $RPM_BUILD_ROOT/%{_libdir}/%{name}/
done

install -m 755 pgfouine.php $RPM_BUILD_ROOT/%{_bindir}/

%pre

%post

%postun

%clean
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT

%files
%defattr(-, root, root)
%doc AUTHORS COPYING INSTALL THANKS README
%attr(0755, root, root) %{_bindir}/pgfouine.php
%{_libdir}/%{name}

%changelog
* Sun May 21 2006 Guillaume Smet <guillaume-pg@smet.org>
- released 0.6
* Sun Mar 26 2006 Guillaume Smet <guillaume-pg@smet.org>
- released 0.5
* Tue Jan 10 2006 Guillaume Smet <guillaume-pg@smet.org>
- released 0.2.1
* Sun Dec 4 2005 Guillaume Smet <guillaume-pg@smet.org>
- released 0.2
* Fri Nov 18 2005 Guillaume Smet <guillaume-pg@smet.org>
- initial RPM packaging