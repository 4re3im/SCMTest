Name: cup-tng-go
Version: %{_version}
Release: %{_release}
BuildArch: noarch
License: Cambridge University Press
Summary: TNG-GO script

%description
Spec file will create an rpm which contains the TNG-GO application

%prep
rm -rf $RPM_BUILD_DIR/*
rm -rf $RPM_BUILD_ROOT/*
cp -r $RPM_SOURCE_DIR/. $RPM_BUILD_DIR/

%build

%install
install --directory $RPM_BUILD_ROOT/mnt/data/www/tng.cambridge.edu.au/
cp -r $RPM_BUILD_DIR/. $RPM_BUILD_ROOT/mnt/data/www/tng.cambridge.edu.au/

%pre

%post

%files
%defattr(775,ec2-user,ec2-user,775)
/mnt/data/www/tng.cambridge.edu.au/