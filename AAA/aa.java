package org.ToMar.pentathlon;

import java.awt.*;
import java.awt.event.*;
import java.util.*;
import org.ToMar.Utils.Functions;
import org.ToMar.Utils.tmButton;
import org.ToMar.Utils.tmColors;
import org.ToMar.Utils.tmFonts;
import org.ToMar.wordlist.WordList;

/*
 * Created on Apr 8, 2005
 * Updated March, 2011 for Facebook API
 * Updated March, 2012 for PHP interface using Google authentication
 */
public class AA extends Canvas implements MouseListener
{
	private static final long serialVersionUID = 1663107798626317480L;
	public static final int SIZE = 17;
	public static final int COLUMNMARGIN = 2;
	public static final int ROWMARGIN = 2;
	public static final int LEFTMARGIN = 2;
	public static final int TOPMARGIN = 32;
	public static final int TOTALCOLS = 3;
	public static final int TOTALROWS = 9;
	public static final int ROUNDER = 3;
    public static final int MAXWORDSIZE = 6;
    public static final int POOLROWS = 5;
	public static final String FILLER = "?";
    public static final int SLOTSPERROW = 23;
    private static final int NUMBEROFSLOTS[] = {2, 3, 3};
	private Word[] words;                               // words in this round
    private int[] xCOORDINATES;                         // MAXWORDSIXE of these, one for each letter in word
    private int[] yCOORDINATES;                         // TOTALROWS + POOLROWS of these
    private ArrayList<Slot> letterPool;                 // slots for unplaced letters
	private ArrayList<String> lettersForMaze;           // letters to place in this round
	private ArrayList<String> wordList;                 // master word list (4, 5, and 6 letters)
	int wordsFormed;
	String message = "";
	Slot selectedSlot;
    Pentathlon pentathlon;
	WordList w;
    private tmButton helpButton;
	private tmButton wordButton;
	private tmButton hintButton;
	private int GAMEINDEX;

    // Classes
    //  Word - has an index, an originalWord string, Anchors, Slots, yCoordinate, and xBase
    //  Anchor - fixed letter in a Word. has an x, y, and Letter.
    //  Slot - where a Letter can be placed. Some are in Word; others make up the letterPool
    //  Letter - to be placed. Must either be in a slot in a Word or the letterPool
    // Slots are the only clickable things
    // When a word is created, anchors are placed, and then each other letter will generate a Slot, a letterPool Slot, and a Letter
    // Board will be divided into 20 size columns
    // First 5 rows of 20 will be letterPool
    // Next 9 rows of 20 will be 6 blank 6 blank 6 for the 27 word slots
    // A slot can hold a slot or an anchor
	public AA(Pentathlon pentathlon, int idx)
	{
		pentathlon.log.debug("AA.constructor");
		this.GAMEINDEX = idx;
        this.pentathlon = pentathlon;
        this.addMouseListener(this);
		this.setSize(Pentathlon.widths[GAMEINDEX], Pentathlon.height);
		wordList = new ArrayList<>();
        w = new WordList();
		for (int i = 4; i < 7; i++)
		{
            wordList.addAll(w.getWordListByLength(i, true));
		}
		yCOORDINATES = new int[TOTALROWS + POOLROWS];
		xCOORDINATES = new int[SLOTSPERROW];
        for (int i = 0; i < TOTALROWS + POOLROWS; i++)
        {
            yCOORDINATES[i] = TOPMARGIN + (SIZE + ROWMARGIN) * i;
		}
        for (int i = 0; i < (SLOTSPERROW); i++)
        {
            xCOORDINATES[i] = LEFTMARGIN + (SIZE + COLUMNMARGIN) * i;
        }
		helpButton = pentathlon.getHelpButton(GAMEINDEX);
		helpButton.setX(2);
        wordButton = new tmButton(Pentathlon.widths[GAMEINDEX] - 50, 5, 45, "WORDS");
        wordButton.setHeight(20);
        wordButton.setFontSize(10);
        wordButton.setFgColor(Pentathlon.bgColors[GAMEINDEX]);
        wordButton.setBgColor(tmColors.DARKGREEN);
        wordButton.setXLabel(5);
        wordButton.setYLabel(14);
        hintButton = new tmButton(Pentathlon.widths[GAMEINDEX] - 80, 5, 27, "HINT");
        hintButton.setHeight(20);
        hintButton.setFontSize(10);
        hintButton.setFgColor(Pentathlon.bgColors[GAMEINDEX]);
        hintButton.setBgColor(tmColors.DARKBLUE);
        hintButton.setXLabel(3);
        hintButton.setYLabel(14);
	}
	public void restore(String s)
	{
		try
		{
		pentathlon.log.debug("AA.restore: " + s + " for level " + pentathlon.getLevel());
		// pentathlon will call this instead of reInit for first level of game if there's input
		String[] levelWords = new String[pentathlon.getLevel()];
		String[] levelPatterns = new String[pentathlon.getLevel()];
		String input = s.substring(s.length() - (pentathlon.getLevel() * 12));
		for (int i = 0; i < pentathlon.getLevel(); i++)
		{
			levelWords[i] = input.substring(i * 12, i * 12 + 6).trim();		// 0	0	6		6	12
			levelPatterns[i] = input.substring(i * 12 + 6, (i+1)*12).trim();	// 1	12	18		18	24
			pentathlon.log.debug(levelWords[i] + " " + levelPatterns[i]);
		}
		makePuzzle(levelWords, levelPatterns);
		} catch (Exception e)
		{
			pentathlon.log.error("Pentathlon.AA.Exception in restore: " + e);
		}
	}
	public void reInit()
	{
		try
		{
		pentathlon.log.debug("AA.reInit");
		String[] levelWords = new String[pentathlon.getLevel()];
		String[] levelPatterns = new String[pentathlon.getLevel()];
        int[] picks = Functions.randomPicks(wordList.size(), pentathlon.getLevel());
		for (int i = 0; i < pentathlon.getLevel(); i++)
		{
			levelWords[i] = wordList.get(picks[i]);
			StringBuilder sb = new StringBuilder(levelWords[i]);
            int[] p = Functions.randomPicks(levelWords[i].length(), NUMBEROFSLOTS[levelWords[i].length() - 4]);
			pentathlon.log.debug("Length: " + levelWords[i].length() + ", anchors: " + NUMBEROFSLOTS[levelWords[i].length() - 4]);
            // place the anchors
            for (int j = 0; j < NUMBEROFSLOTS[levelWords[i].length() - 4]; j++)
            {
				pentathlon.log.debug("AA.reInit: letterIndex is " + p[j]);
				sb.setCharAt(p[j], FILLER.charAt(0));
            }
			levelPatterns[i] = sb.toString();
		}
		makePuzzle(levelWords, levelPatterns);
		} catch (Exception e)
		{
			pentathlon.log.error("Pentathlon.AA.Exception in reInit: " + e);
		}
	}
	public void makePuzzle(String[] lWords, String[] lPatterns)
	{
		pentathlon.log.debug("AA.makePuzzle");
		wordsFormed = 0;
		message = "";
		lettersForMaze = new ArrayList<>();
        letterPool = new ArrayList<>();
		words = new Word[pentathlon.getLevel()];
        // This creates the words, which will fill lettersForMaze
		// also run each word through the pattern filter for the help word list
		ArrayList<String> levelList = new ArrayList<>();
		for (int i = 0; i < pentathlon.getLevel(); i++)
		{
			words[i] = new Word(i, lWords[i], lPatterns[i]);
			levelList.add(lPatterns[i]);
			levelList.addAll(w.getWordListByPattern(words[i].getValue(), true));
			levelList.add("------------");
		}
		pentathlon.setWordList(levelList, levelList.size(), 20);
		// Now sort the lettersForMaze into alphabetical order
        // This sorts the letterPool into alphabetical order
		boolean flips = true;
		while (flips == true)
		{
			flips = false;
			for (int i = 0; i < lettersForMaze.size() - 1; i++)
			{
				if (lettersForMaze.get(i).compareTo(lettersForMaze.get(i + 1)) > 0)
				{
					String temp = lettersForMaze.get(i);
					String temp1 = lettersForMaze.get(i + 1);
					lettersForMaze.set(i, temp1);
					lettersForMaze.set(i + 1, temp);
					flips = true;
				}
			}
		}
		for (int i = 0; i < lettersForMaze.size(); i++)
		{
			letterPool.add(new Slot(i));
			letterPool.get(i).setLetter(new Letter(i));
		}
        pentathlon.setPiecesToFind(GAMEINDEX, lettersForMaze);
        pentathlon.setActive(GAMEINDEX, false);
        message = "Find " + lettersForMaze.size() + " letters.";
		selectedSlot = null;
		repaint();
	}
	public String getSaveString()
	{
		pentathlon.log.debug("AA.getSaveString");
		StringBuilder sb = new StringBuilder("");
		for (int i = 0; i < words.length; i++)
		{
			sb.append(words[i].getSaveString());
		}
		return sb.toString();
	}
    public void setMessage(String message)
    {
        this.message = message;
        repaint();
    }
	public void mouseClicked(MouseEvent e)
	{
        if (helpButton.clicked(e.getX(), e.getY()))
        {
            pentathlon.getHelp(GAMEINDEX);
        }
		else if (pentathlon.isActive()[GAMEINDEX] && !pentathlon.isSolved()[GAMEINDEX])
        {
			if (wordButton.clicked(e.getX(), e.getY()))
			{
				pentathlon.seeWordList(true);
			}
			else if (hintButton.clicked(e.getX(), e.getY()))
			{
				for (int targetWord = 0; targetWord < pentathlon.getLevel(); targetWord++)
				{	// look at each element to see if it's a slot
					for (int targetWordLetter = 0; targetWordLetter < words[targetWord].getElements().length; targetWordLetter++)
					{
						// if it's a slot, if the letter isn't the one in the original word, return it
						if ("org.ToMar.pentathlon.AA$Slot".equalsIgnoreCase(words[targetWord].getElements()[targetWordLetter].getClass().getName()))
						{
							if (((Slot) words[targetWord].getElements()[targetWordLetter]).getLetter() != null)
							{
								if (!(((Slot) words[targetWord].getElements()[targetWordLetter]).getLetter().getLetter().equalsIgnoreCase(words[targetWord].getOriginalWord().substring(targetWordLetter, targetWordLetter + 1))))
								{	// put the letter back
									((Slot) words[targetWord].getElements()[targetWordLetter]).getLetter().putBack();
									((Slot) words[targetWord].getElements()[targetWordLetter]).setLetter(null);
								}
							}
						}
					}
				}
			}
		  	else
			{
				message = "Words: " + wordsFormed;
				// this is looking at each slot in the letter pool
				// if you click on a slot in the pool
				//     if the slot is empty
				//        if a slot has already been selected
				//			move its letter from that slot to this slot
				//		  otherwise, no action
				//     if there's a letter in the slot
				//		  if this letter was already in selected state, unselect it
				//        else if another slot has already been selected
				//			de-select that one, and select this one
				for (int slotIndex = 0; slotIndex < letterPool.size(); slotIndex++)
				{
					Slot s = letterPool.get(slotIndex);
					pentathlon.log.debug("Looking at pool slot " + slotIndex + ", current status is " + s.getStatus());
					if (s.clicked(e.getX(), e.getY()))				// if slot was clicked
					{
						if (s.getLetter() != null)					// if there's a letter in the slot
						{
							pentathlon.log.debug("found");
							// if this letter was already selected, and you click again, deselect it
							if (s.getStatus() == Slot.POOLSELECTED)
							{
								deselect();
							}
							else if (s.getStatus() != Slot.POOLUSED)
							{ 	// if another letter was already selected, deselect it
								if (selectedSlot != null)
								{
									deselect();
								}
								// now select this one
								selectedSlot = s;
								s.setStatus(Slot.POOLSELECTED);
							}
							pentathlon.log.debug("Looking at pool slot " + slotIndex + ", status is now " + s.getStatus());
							repaint();
						}
						return;
					}
				}
				wordsFormed = 0;
				// this is looking at the slots in each word on the word list
				for (int targetWord = 0; targetWord < pentathlon.getLevel(); targetWord++)
				{	// look at each element to see if it's a slot
					for (int targetWordLetter = 0; targetWordLetter < words[targetWord].getElements().length; targetWordLetter++)
					{
						pentathlon.log.debug("looking at letters: " + targetWordLetter + ": " + words[targetWord].getElements()[targetWordLetter].getClass().getName());
						if (words[targetWord].getElements()[targetWordLetter].getClass().getName().equals("org.ToMar.pentathlon.AA$Anchor"))
						{
							pentathlon.log.debug("In word " + targetWord + ", bypassing anchor letter " + targetWordLetter + " = " + words[targetWord].getElements()[targetWordLetter]);
							continue;
						}
						// for each letterHolder (non-anchor) within the word
						Slot holder = (Slot) words[targetWord].getElements()[targetWordLetter];
						if (holder.clicked(e.getX(), e.getY()))
						{	// you've clicked on a destination
							Letter currentLetter = holder.getLetter();
							if (currentLetter != null)
							{	//there's already a letter here - return it to the pool
								currentLetter.putBack();
								holder.setLetter(null);
							}
							if (selectedSlot != null)
							{
								if (selectedSlot.getLetter() != null)
								{
									holder.setLetter(selectedSlot.getLetter());
									selectedSlot.setStatus(Slot.POOLUSED);
									selectedSlot = null;
								}
							}
							repaint();
							break;
						}
					}
					words[targetWord].setGoodWord(false);
					String s = words[targetWord].getValue();
					if (s.indexOf(FILLER) == -1)
					{
						for (int i = 0; i < wordList.size(); i++)
						{
							if (wordList.get(i).equalsIgnoreCase(s))
							{
								wordsFormed += 1;
								words[targetWord].setGoodWord(true);
								break;
	//							log("word found = " + words[targetWord].getValue() + " making " + wordsFormed + " words");
							}
						}
					}
				}
				if (wordsFormed == pentathlon.getLevel())		// beat the pentathlon.getLevel()
				{
					message = "You got it!";
					for (int i = 0; i < letterPool.size(); i++)
					{
						letterPool.set(i, null);
					}
					pentathlon.seeWordList(false);
					pentathlon.setSolved(GAMEINDEX, true);
					repaint();
				}
				else
				{
					message = "Words: " + wordsFormed;
				}
			}
		}
		repaint();
	}
	public void deselect()
	{
		if (selectedSlot != null)
		{
			pentathlon.log.debug("AA.deselect, selectedSlot is " + selectedSlot + ", current status is " + selectedSlot.getStatus());
			selectedSlot.setStatus(Slot.POOLUNSELECTED);
			selectedSlot = null;
		}
	}
	public void mouseExited(MouseEvent e){}
    public void mousePressed(MouseEvent e) {    }
	public void mouseReleased(MouseEvent arg0)	{	}
	public void mouseEntered(MouseEvent arg0)	{	}

	public void paint(Graphics og)
	{
        helpButton.draw(this.getGraphics());
  		if (pentathlon.isActive()[GAMEINDEX] && !pentathlon.isSolved()[GAMEINDEX])
        {
			wordButton.draw(this.getGraphics());
			hintButton.draw(this.getGraphics());
		}
        og.setColor(tmColors.BLACK);
        og.setFont(tmFonts.PLAIN24);
        og.drawString(Pentathlon.titles[GAMEINDEX], 50, 25);
		og.setFont(tmFonts.PLAIN16);
        og.drawString(message, 200, 25);
		if (!pentathlon.isGameOver())
		{
			for (int i = 0; i < letterPool.size(); i++)
			{
				// if it holds a letter, draw it
				Slot s = letterPool.get(i);
				if (s != null && s.getLetter() != null)
				{
					s.draw(og, false);
				}
			}
			for (int i = 0; i < pentathlon.getLevel(); i++)
			{
				words[i].draw(og);
			}
			if (pentathlon.isSolved()[GAMEINDEX])
			{
				for (int targetWord = 0; targetWord < pentathlon.getLevel(); targetWord++)
				{
					words[targetWord].drawOriginalWord(og);
				}
			}
		}
	}
    public void foundPiece(int index)
    {
		pentathlon.log.debug("AA.foundPiece: " + index);
        letterPool.get(index).setStatus(Slot.POOLUNSELECTED);
        repaint();
    }

	public void update(Graphics g)
	{
		pentathlon.setBgColor(g, GAMEINDEX);
		g.fillRect(0, 0, Pentathlon.widths[GAMEINDEX], Pentathlon.height);
		paint(g);
	}
	private class Word
    {
        private Object[] elements;          //composed of Anchors and Slots
        private String originalWord;
        private int index;
        private boolean goodWord;

        public Word (int index, String originalWord, String pattern)
        {
			pentathlon.log.debug("AA.Word.constructor: " + originalWord + ", " + pattern);
            this.index = index;
            this.originalWord = originalWord;
            int row = index / TOTALCOLS;
            int col = (index % TOTALCOLS) * (MAXWORDSIZE + 2);
            elements = new Object[originalWord.length()];
            // use the pattern to place the anchors
			// if the char is FILLER, it's a slot; otherwise, an anchor
            for (int i = 0; i < originalWord.length(); i++)
            {
				String ch = pattern.substring(i, i+1);
				String orig = originalWord.substring(i, i+1);
				if (FILLER.equalsIgnoreCase(ch))
				{
	                elements[i] = new Slot(xCOORDINATES[col + i], yCOORDINATES[row + POOLROWS]);
					lettersForMaze.add(orig);
				}
				else
				{
					Letter l = new Letter(orig);
					elements[i] = new Anchor(l, xCOORDINATES[col + i], yCOORDINATES[row + POOLROWS]);
				}
			}
        }

		public String getOriginalWord()
		{
			return originalWord;
		}
        public Object[] getElements()
        {
            return elements;
        }
		public String getSaveString()
		{
			pentathlon.log.debug("AA.Word.getSaveString");
			//should be run at beginning of level, all words saved as 6 chars
			StringBuilder sb = new StringBuilder();
			sb.append((originalWord + "   ").substring(0,6));
			sb.append((this.getValue() + "   ").substring(0,6));
			return sb.toString();
		}
        public String getValue()
        {
			pentathlon.log.debug("AA.Word.getValue: " + this.originalWord);
            String returnWord = "";
            for (int i = 0; i < elements.length; i++)
            {
                if (elements[i].getClass().getName().equals("org.ToMar.pentathlon.AA$Anchor"))
                {
                    returnWord += ((Anchor) elements[i]).getLetter().getLetter();
                }
                else if (((Slot) elements[i]).getLetter() != null)
                {
                    returnWord += ((Slot) elements[i]).getLetter().getLetter();
                }
                else
                {
                    returnWord += FILLER;
                }
            }
			pentathlon.log.debug("AA.Word.getValue(result): " + returnWord);
            return returnWord;
        }
        public void drawOriginalWord(Graphics og)
        {
			pentathlon.log.debug("AA.Word.drawOriginalWord");
            og.setFont(tmFonts.BOLD12);
            og.setColor(tmColors.DARKBLUE);
            int x = LEFTMARGIN + (index % 5) * 80;
            int y = 45 + 15 * (index / 5);
            og.drawString(originalWord, x, y);
        }
        public void draw(Graphics og)
        {
            try
            {
                for (int i = 0; i < elements.length; i++)
                {
                    elements[i].getClass().getMethod("draw", Graphics.class, boolean.class).invoke(elements[i], og, goodWord);
                }
            }
            catch (Exception e)
            {
                pentathlon.log.error("AA.draw: " + e);
            }
        }
		public boolean isGoodWord()
		{
			return goodWord;
		}
		public void setGoodWord(boolean goodWord)
		{
			this.goodWord = goodWord;
		}
    }
    private class Letter
    {
        private String letter;
		private int slotNumber;

		public Letter(int slot)
		{
			pentathlon.log.debug("AA.Letter.constructor(slot): " + slot);
			slotNumber = slot;
			letter = lettersForMaze.get(slot);
		}
        public Letter(String letter)
        {
			pentathlon.log.debug("AA.Letter.constructor(letter): " + letter);
		    this.letter = letter;
        }
		public void putBack()
		{
			letterPool.get(slotNumber).setStatus(Slot.POOLUNSELECTED);
		}
		public int getSlotNumber()
		{
			return slotNumber;
		}
        public void draw(Graphics og, int x, int y, boolean goodWord)
        {
            og.setFont(tmFonts.BOLD16);
			if (goodWord)
			{
				og.setColor(tmColors.DARKORANGE);
			}
			else
			{
				og.setColor(tmColors.BLACK);
			}
            og.drawString(letter, x + 3 + getXFactor(letter), y + 15);
        }
        public String getLetter()
        {
            return letter;
        }
		private int getXFactor(String l)
		{
			if ("W".indexOf(l) > -1)
			{
				return -2;
			}
			else if ("M".indexOf(l) > -1)
			{
				return -1;
			}
			else if ("I".indexOf(l) > -1)
			{
				return 3;
			}
			else if ("J?*".indexOf(l) > -1)
			{
				return 2;
			}
			else if ("AELYTP".indexOf(l) > -1)
			{
				return 1;
			}
			else if ("R".indexOf(l) > -1)
			{
				return 1;
			}
			return 0;
		}
	}
    private class Slot
    {
		public final static int INWORD = 0;				// cream, if there's a letter, show it
		public final static int POOLINACTIVE = 1;		// chartreuse, blank
		public final static int POOLUNSELECTED = 2;		// chartreuse, show letter
		public final static int POOLSELECTED = 3;		// yellow, show letter
		public final static int POOLUSED = 4;			// dark gray, show letter
        private Letter letter;
        private int x;
        private int y;
		private int status;

        public Slot (int x, int y)                  // this constructor comes from slots in a word
        {
			pentathlon.log.debug("AA.Slot.constructor(x, y)");
            this.x = x;
            this.y = y;
            this.letter = null;
			status = INWORD;
        }
        public Slot(int index)                      // this constructor for letterPool slots
        {
			pentathlon.log.debug("AA.Slot.constructor(index): " + index);
            this.letter = null;
            this.x = xCOORDINATES[index % SLOTSPERROW];
            this.y = yCOORDINATES[index / SLOTSPERROW];
			status = POOLINACTIVE;
//			pentathlon.log("pSlot, x = " + x + ", y = " + y);
        }
		public boolean clicked(int x, int y)
		{
			if (x > this.x &&
				x < this.x + SIZE &&
				y > this.y &&
				y < this.y + SIZE)
			{
				return true;
			}
			return false;
		}
        public void draw(Graphics og, boolean goodWord)
        {
            if (this.letter != null)
            {
                if (status == POOLSELECTED)
                {
                    og.setColor(tmColors.GOLD);
                }
				else if (status == POOLUSED)
                {
                    og.setColor(tmColors.DARKGRAY);
                }
                else
                {
                    og.setColor(tmColors.PALECHARTREUSE);
                }
            }
            else
            {
                og.setColor(tmColors.CREAM);
            }
            og.fillRoundRect(x, y, SIZE, SIZE, AA.ROUNDER, AA.ROUNDER);
            og.setColor(tmColors.BLACK);
            og.drawRoundRect(x, y, SIZE, SIZE, AA.ROUNDER,AA.ROUNDER);
            if (this.letter != null)
            {
				if (status != POOLINACTIVE)
				{
					this.letter.draw(og, x, y, goodWord);
				}
            }
        }
        public Letter getLetter()
        {
            return letter;
        }
        public void setLetter(Letter letter)
        {
            this.letter = letter;
        }
		public int getStatus()
		{
			return status;
		}
		public void setStatus(int status)
		{
			pentathlon.log.debug("Slot.setStatus for slot " + this.letter.getSlotNumber() + ": coming in with status of " + this.status);
			this.status = status;
			pentathlon.log.debug("Slot.setStatus for slot " + this.letter.getSlotNumber() + ": going out with status of " + this.status);
		}
    }
    private class Anchor
    {
        Letter letter;
        int x;
        int y;

        public Anchor(Letter letter, int x, int y)
        {
			pentathlon.log.debug("AA.Anchor.constructor: " + letter);
            this.letter = letter;
            this.x = x;
            this.y = y;
        }
        public void draw(Graphics og, boolean goodWord)
        {
            letter.draw(og, x, y, goodWord);
        }
        public Letter getLetter()
        {
            return this.letter;
        }
    }
}
