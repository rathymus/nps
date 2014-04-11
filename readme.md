# The Notation Parsing System

Contents

1. Agenda
2. Project Status
3. Comparison with Markdown and Creole
4. Syntax

## Agenda

Let's set out what NPS is and what it isn't.

**What is NPS** NPS is a web-oriented feature-rich content authoring system
comparable to LaTeX in functionality and Creole / Markdown in simplicity.

**What it isn't** Many people have talked about how Markdown suffers from
a lack of leadership. Case in point, every major website has its own flavor.
As a result many people have written MD replacements with names such as Rockdown
or MultiMarkdown, to name a few. NPS is _not_ a MD replacement. It _can_ be used
for that purpose, by using just a subset of the power it aims to provide.

## Project Status

The project is currenlty at its infancy. Source code in this repository is
intended to be a proof-of-concept, mostly for demonstration purposes.

When (if) the project gains some support, the following items must be discussed:
* Better name (current one sucks. Anything with {*'s Not \[_markup system_]} or
{Yet Another *} is prohibited.)

* Freeze syntax. We borrow, beg and steal the best syntax conventions
out there. _And we're proud of it_.

### Workplan

Current version is 0.1. Next version will be 0.5 and to reach it the following
items must be completed:

* All function points in Syntax (see below)
* Something resembling a test suite

at which point some marketing and promotion is due. To reach version 1.0 NPS
should be feature-complete, with the following features:

* Indexing
* Reference support
* Music and math notation

### Future Additions

There are some notations which have been neglected. These are maths and music
notations. For maths there are several packages, mostly in Javascript,
rendering content as the page loads. For music, well, I found a proposal called
MML, but apart from that nothing (but didn't look really hard either). 

## Comparison with Markdown and Creole

This section is aimed to giving the reader the intuition behind syntax choices
by comparing MD and Creole with NPS, and with each other. Yes I'm aware that
this document is written in MD (I wrote it after all), and yes, I also see the
irony.

Let's see what Markdown wants to accomplish,
[in the words of its creator](http://daringfireball.net/projects/markdown/):

> Readability, however, is emphasized above all else. A Markdown-formatted
> document should be publishable as-is, as plain text, without looking like it's
> been marked up with tags or formatting instructions.

and has done a fatastic job in that respect.

Creole's [goals](http://www.wikicreole.org/wiki/Goals) page is much lengthier
and thus not quoted here but definitely worth a read. Go ahead. The thing to
take away though is:

* Fast to type
* Readable
* Easy to teach/learn
* Not reliant on whitespaces

are not in the top 4 goals. I've decided to drop ease or learning as well but
that doesn't mean making NPS esoteric. NPS though should _definitely_ be fast
to type and not reliant on whitespaces.

NPS shares, or has been influenced by, Markdown's and Creole's simple and
natural way of doing markup. 

### Inline HTML

HTML tags are ignored in NPS. Well, let me rephrase: HTML angle brackets are
made to be ignored by the browser.

### Headers

NPS doesn't support underlined headers. In MD you can do this:

	# Heading
	## Subheading

or this

	Heading
	=======
	Subheading
	----------

Creole does something similar with

	= Heading
	== Subheading

Underlining presents the problem of forcing a one-dimensional document
structure. It's visually appealing when viewing the source (which goes along
with its design specs) but that's about it.

### Things to take home 

* Creole uses non-whitespace markup to force line breaks (`\\`). MD does the
same thing with trailing spaces. Creole wins seems to do it better.

* MD has no table support out of the box. Creole does so with `|`. Tables are
a pain anyway, but since Emacs' Org mode supports on-the-fly column alignment,
we'll copy that. Creole doesn't appear to have text alignment though, which is
something that needs to be addressed.

## Syntax

The basic NPS unit is a paragraph. That's not necessarily a text paragraph
surrounded by, say, `<p></p>`, but a distinct logical unit within the text.
To avoid confusion we say the fundemental unit is a **context** (and yes, a text
paragraph is a context).

Specification conflicts and weird edge-cases produce undefined behaviour. The
defined behaviour for an undefined behaviour is to produce nothing and complain
about it in stderr or equivalent. It goes without saying, such cases should
be filed as bugs, and hopefully they'll be taken care of.

### Basics

* Bold:  
`*wrapped in single stars*` **wrapped in single stars**
* Italics:  
`_wrapped in single underscores_` _wrapped in single underscores_
* Underlined:  
`__wrapped in double underscores__` <u>wrapped in double underscores</u>
* Crossed-out:  
`---wrapped in triple dashes---` <strike>wrapped in triple dashes</strike>
* All together:
`__---_*That's a bit overkill isn't it?*_---__`
<strike><u><i><b>That's a bit overkill isn't it?</b></i></u></strike>

By induction, three underscores in a row underline and italicize. Inline `code`
should be wrapped in single backticks `.

The backslash character `\` escapes the next one, and double backslashes
force a line break (spec conflict?)

Finally three dashes in a row `---`, in a line of their own produce a horizontal
line:

---

##Support for other markups

NPS markup syntax attempts to offer the "best of all worlds" but NPS is
ultimately _not_ a markup language. It's a document authoring system. Thus
support for other markups should be possible.

_Caveat:_ To achieve this, the user must select which modules the alternative
markup will take over. The basic markup (bold, italcs etc.) can be taken over
painlessly; inline code however could be a bit of a struggle. That's because
NPS doesn't rely on whitespace, and often trims the input, possibly making
languages such as Markdown fail in those cases.

### Linking

These two aren't the only ways to link:

* `[This is a link http://example.com]` [This is a link](http://example.com)
* `[http://example.com This is also a link]`
  [This is also a link](http://example.com)

But:

* `[This produces bracket-enclosed text]` [This produces bracket-enclosed text]
* `[Link to the same website /page.html]` Local link must start with a slash `/`
* `[http://example.com/on_its_own]`
<a href="http://example.com/on_its_own">http://example.com/on_its_own</a>

Notice in the first two examples there's no need to explicitly tell NPS about
a link; as long as a URL starts with a recognized protocol, NPS will figure it
out. Recognized protocols are

* `http(s)://`
* `ftp(s)://`

Don't try and be clever by doing something like
`[http://evil.com http://example.com]` - that's undefined behaviour, explicitly
so, to prevent attacks like this.

The URL should't contain whitespace. That character should be inserted in its
hex representation `%20`. If you copy a URL that contains spaces from a modern
browser's address bar, the browser will take care of the conversion for you, but
it's good to keep an eye out for it.

For example,

	[http://example.com/my whitespace page.html A link containing whitespace]
	
produces
[whitespace page.html A link containing whitespace](http://example.com/my)

To link to a label within the current document:

* `[Link to label 'label]`

For example

	['interesting-bit
	This is the interesting bit.

	In another paragraph I reference a [bit of interest 'interesting-bit].

produces

	<p id="interesting bit">
	This is the interesting bit.
	</p>
	
	<p>
	In another paragraph I reference a <a href="#interesting-bit">bit of
	interest.</a>
	</p>


### Headers & Lists

Syntax for headers and lists has been heavily influenced by Creole.

* `=. This is the title` 
* `==. This is a subtitle`

Notice the dot after the equals sign. Titles and subtitles are meant to be
used only once per document. Using them multiple times is undefined behaviour.

To add document structure use the equals `=` sign at the beginning of a
sentence. More signs deepens the structure nesting. The notation for this
section could be

	= Syntax
	== Basic
	== Linking
	== Headers & Lists
	== Contexts and Labels
	=== Implied Labels
	etc.


### Contexts and Labels

A context may have sub-contexs. For example a list may contain some paragraphs.
By default a context is a text paragraph. Each context is identified by a
set of special characters on the first line. How a context ends depends on, well,
the context. Consider the following:

	This is a text block. It will be rendered as a text paragraph and is
	terminated by two consecutive newlines (\n\n). By the way the first thing
	the official parser does is convert newlines to Unix-style.

---

What if we wanna appear clever? We quote someone famous!

	> This is a quote. The parser knows this because the first character is
	a right-angle bracket. Each line in a quote doesn't have to start with
	that character

	> but consecutive quote blocks are treated as different blocks if separated
	by more than one line break. *Formatting* and [linking http://example.com]
	are permitted within a quote.


> This is a quote. The parser knows this because the first character is
> a right-angle bracket. Each line in a doesn't have to start with that character

> but consecutive quote blocks are treated as different blocks if separated
> by more than one line break. **Formatting** and [linking](http://example.com)
> are permitted within a quote.

---

Code blocks start with double backticks, and end with them as well.

	`` [!lang:c
	int foo (){
		printf ("Hello world!\n");
		}
	``

produces

	int foo (){
		printf ("Hello world!\n");
	}

The `!lang` keyword tells NPS to color the following block as a C snippet. NPS
doesn't really color a code block - it offloads it to another open-source
module. Thus NPS isn't really bothered by the presence or absense of `!lang`,
and availability of syntaxes depend entirely on said third-party project.

### Labels

A label is a way of adressing some context from another. They are declared
by the single quote `'` in a Meta field, are case-insensitive and can contain
any alphanumeric character, plus the underscore `_`.

	== Validity of a Youtube Comment ['val
	
	> ['einst @Albert Einstein !1946 "During keynote in the Ministry of Truth 
	I have not said any of the things people attribute to me on the Internet

	As Einstein said in ['einst], people on the Internet sometimes make stuff up

	['val] is dedicated to this sort of thing.

produces

> I have not said any of the things people attribute to me on the Internet
*Albert Einstein, 1946, During keynote in the Ministry of Truth*

As Einstein said in *Quote 1*, people on the Internet sometimes make stuff up

Section 2.1 is dedicated to this sort of thing.

The default behaviour of labels are to create HTML anchor points. The following
paragraph


	
	
#### Implied Labels

Every time you create a header you also produce a label. Implied labels are
always prefixed with `h_` and all non-alphanumeric characters are reduced
to `_`.

Headers that reduce to the same implied label get the same label. It's left to
the document author to explicitly declare a header label to avoid collisions.

For example

	== This is a subsection!
	== Same
	== Same

produces

	h_this_is_a_subsection_
	h_same
	h_same

Of course one can assign a custom label, thus removing the implied one from the
precedence list. Explicitly assigning a label to a header removes the `h_`
prefix from it.

	= Header ['header
	= Header

produces

	header
	h_header
