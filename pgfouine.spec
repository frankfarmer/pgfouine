Summary:	PgFouine PostgreSQL log analyzer
Name:		pgfouine
Version:	0.7
Release:	2%{?dist}
BuildArch:	noarch
License:	GPL
Group:		Development/Tools
Source0:	http://pgfoundry.org/frs/download.php/1041/%{name}-%{version}.tar.gz
URL: 		http://pgfouine.projects.postgresql.org
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)

AutoReqProv:	off

Requires:	php

Patch1:		pgfouine-0.7-include_path.patch

%description
pgFouine is a PostgreSQL log analyzer. It generates text 
or HTML reports from PostgreSQL log files. These reports 
contain the list of the slowest queries, the queries that 
take the most time and so on.

pgFouine can also:
- analyze VACUUM VERBOSE output to help you improve your 
VACUUM strategy,
- generate Tsung sessions file to benchmark your 
PostgreSQL server.

%prep
%setup -q 
%patch1 -p0

%build

%install
# cleaning build environment
[ "%{buildroot}" != "/" ] && rm -rf %{buildroot}

# creating required directories
install -m 755 -d %{buildroot}/%{_datadir}/%{name}
install -m 755 -d %{buildroot}/%{_bindir}

# installing pgFouine
for i in include tests version.php; do
	cp -rp $i %{buildroot}/%{_datadir}/%{name}/
done

install -m 755 pgfouine.php %{buildroot}/%{_bindir}/
install -m 755 pgfouine_vacuum.php %{buildroot}/%{_bindir}/

%clean
[ "%{buildroot}" != "/" ] && rm -rf %{buildroot}

%files
%defattr(-, root, root)
%doc AUTHORS COPYING INSTALL THANKS README
%attr(0755, root, root) %{_bindir}/pgfouine.php
%attr(0755, root, root) %{_bindir}/pgfouine_vacuum.php
%{_datadir}/%{name}

%changelog
* Thu Aug 17 2006 Devrim Gunduz <devrim@CommandPrompt.com> - 0.7-2
- fixed rpmlint warnings, and made cosmetic changes
* Thu Aug 17 2006 Guillaume Smet <guillaume-pg@smet.org>
- released 0.7
* Thu Aug 10 2006 Guillaume Smet <guillaume-pg@smet.org>
- fixed RPM packaging for 0.7
* Wed Jul 19 2006 Guillaume Smet <guillaume-pg@smet.org>
- added pgfouine_vacuum.php
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
