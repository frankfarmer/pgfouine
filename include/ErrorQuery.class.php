<?php

/*
class ErrorQuery < Query

	attr_reader :text, :hint, :detail, :error

	is_select = false
	is_delete = false
	is_insert = false
	is_update = false

	def initialize(text="NO ERROR MESSAGE")
		@error = text
		@hint = ''
		@detail = ''

		super("NO STATEMENT")
	end

	def append_statement(text)
		$stderr.puts "NIL txt for error statement" if text.nil? && DEBUG
		@text=text
	end
	def append_hint(text)
		$stderr.puts "NIL txt for error hint" if text.nil? && DEBUG
		@hint = text
	end

	def append_detail(text)
		$stderr.puts "NIL txt for error detail" if text.nil? && DEBUG
		@detail = text
	end
	def accumulate_to(accumulator)
		accumulator.append_error(self)
	end

end
*/

?>