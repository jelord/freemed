#!/usr/bin/perl
# $Id$
#
# Authors:
#      Jeff Buchbinder <jeff@freemedsoftware.org>
#
# FreeMED Electronic Medical Record and Practice Management System
# Copyright (C) 1999-2009 FreeMED Software Foundation
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

use Data::Dumper;

my $columns = shift;

my @cols = split /,/, $columns;

while (<>) {
	chomp ( my $line = $_ );
	my $output = "";
	my $first = 1;
	foreach my $offset (@cols) {
		my ($b, $e) = split /-/, $offset;
		if (!$first) { $output .= ","; } else { $first = 0; }
		my $chunk = "";
		$chunk = substr $line, $b-1, ($e-$b)+1;
		$chunk =~ s/^\s+//g; $chunk =~ s/\s+$//g;
		$output .= '"' . $chunk . '"' ;
	}
	$output .= "\n";
	print $output;
}

