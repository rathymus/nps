# The Notation Parsing System

Contents

1. Agenda
2. Project Status
3. Comparison with Markdown and Creole
4. Syntax

## Agenda

Let's set out what NPS is and what it isn't.

**What is NPS** NPS is a feature-rich content authoring system which doesn't
rely on runtime rendering.

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
same thing with trailing spaces. **Creole wins**.

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
a link; as long as a URL starts with a recognized protocol, it'll get recognized
as a link automatically.

Don't try and be clever by doing something like
`[http://evil.com http://example.com]` - that's undefined behaviour. Also if the
URL contains whitespace, it should be the **second** element enclosed.
For example,

	[http://example.com/my whitespace page.html A link containing whitespace]
	
produces
[whitespace page.html A link containing whitespace](http://example.com/my)

### Contexts and Labels

A context may have sub-contexs. For example a list may contain some paragraphs.
By default a context is a text paragraph. Each context is identified by a
set of special characters on the first line. How a context ends depends on, well,
the context. Consider the following:

	This is a text block. It will be rendered as a text paragraph and is
	terminated by two consecutive newlines (\n\n). By the way the first thing a
	parser should do is convert newlines to Unix-style.

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

	``
	int foo (){
		printf ("Hello world!\n");
		}
	``

produces

	int foo (){
		printf ("Hello world!\n");
	}
