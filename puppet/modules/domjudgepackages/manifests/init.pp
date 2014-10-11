# == Class: domjudgepackages
#
# Installs DOMjudge packages
#
class domjudgepackages {
	package { [
		'gcc',
		'g++',
		'make',
		'zip',
		'unzip',
		'php5-json',
		'bsdmainutils',
		'phpmyadmin',
		'ntp',
		'libboost-regex-dev',
		'libgmp3-dev',
		'linuxdoc-tools',
		'linuxdoc-tools-text',
		'groff',
		'texlive-latex-recommended',
		'texlive-latex-extra',
		'texlive-fonts-recommended',
		'texlive-lang-dutch',
		'sudo',
		'debootstrap',
		'procps',
		'gcj-jre-headless',
		'gcj-jdk',
		'openjdk-7-jre-headless',
		'openjdk-7-jdk',
		'ghc',
		'fp-compiler',
		'libcurl4-gnutls-dev',
		'libjsoncpp-dev',
		'libmagic-dev',
		'flexc++',
		'bisonc++',
		'git'
		]:
		ensure => present;
	}
}
