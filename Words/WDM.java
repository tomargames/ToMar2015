/*
	A basic extension of the java.applet.Applet class
 */
import java.awt.*;
import java.applet.*;
import java.awt.event.*;
import java.io.*;
import java.util.*;
import java.net.*;

public class WDM extends Applet implements MouseListener,Runnable,KeyListener
{
	private static final long serialVersionUID = 6427565796071372745L;
	private static final String VERSION = "3.1";
	private static final int NOTSTARTED = 0;
	private static final int GAMEINPROGRESS = 1;
	private static final int GAMEOVER = 2;
	public static final int WIDTH = 800; 
	public static final int HEIGHT = 600;
	private Image offscreenImg;
	private Graphics og;
	ToMarButton mixButton;
	ToMarButton enterButton;
	ToMarButton clearLetter;
	ToMarButton clearWord;
	ToMarButton newPuzzle;
	Color timeColor;
	int secondsLeft;
	long startSec;
	String message;
	Puzzle puzzle;
	Thread thread;
	int gameStage;
	String encName;
	
	
	public static void log(String s)
	{
		System.out.println(s);
	}
	public void init()
	{
		message = this.getParameter("message");
		message = (message == null) ? "Welcome to WordMania!" : message;
		encName = (this.getParameter("nm")).replaceAll(" ", "%20");
		this.addMouseListener(this);
		this.addKeyListener(this);
		this.setSize(WIDTH, HEIGHT);
		this.setBackground(ToMarUtils.toMarBackground);
        offscreenImg = createImage(WIDTH, HEIGHT);
        og = offscreenImg.getGraphics();
		newPuzzle = new ToMarButton(10, 240, 140, "Give Up");
		mixButton = new ToMarButton(160, 240, 140, "Re-Mix");
		clearLetter = new ToMarButton(310, 240, 140, "Clear Letter");
		clearWord = new ToMarButton(460, 240, 140, "Clear Word");
		enterButton = new ToMarButton(610, 240, 140, "Start");
		enterButton.setBgColor(new Color(125,255,125));
		gameStage = NOTSTARTED;
	}	

	private void setUpGame()
	{
		message = "Setting up a new game.";
		enterButton.setLabel("Submit Word");
		puzzle = new Puzzle(this);
		gameStage = GAMEINPROGRESS;
		timeColor = Color.black;
		startSec = (new Date()).getTime();
		thread = new Thread(this);
		thread.start();
	}
	public void run()
	{
		while (gameStage == GAMEINPROGRESS)
		{
			secondsLeft = puzzle.getTimeAllowed() - (int)((new Date()).getTime() - startSec)/1000;
			if (secondsLeft < 0)
			{
				secondsLeft = 0;
				endGame(false);
			}
			else if (secondsLeft < 11)
			{	
				timeColor = Color.red;
			}
			repaint();
		}	
	}	
	private void endGame(boolean allWordsFound)
	{	
		gameStage = GAMEOVER;
		int points = puzzle.getPoints();
		if (points > 0)
		{	
			if (allWordsFound)
			{	
				int b = puzzle.getBonus() + (secondsLeft * 10);
				points += b;
				message = "You got them all! Your bonus is " + b + ", giving you " + points + "!";
			}
			else
			{
				message = "Time's up! You scored " + points + ".";
			}
			repaint();
			try
			{
				Thread.sleep(5000);
				String fwd = this.getParameter("site") + "WDM?score=" + points + "&id=" + this.getParameter("id") + "&nm=" + encName;
				this.getAppletContext().showDocument(new URL(fwd));
			}
			catch(Exception e)
			{
				log("Error 1: " + e);
			}
		}
		enterButton.setEnabled(false);
		repaint();
	}
	public void mousePressed(MouseEvent e)
	{
		int x = e.getX();
		int y = e.getY();
		if (gameStage == GAMEINPROGRESS)
		{	
			if (newPuzzle.clicked(x,y))
			{
				if (puzzle.getPoints() == 0 ||"Confirm".equals(newPuzzle.getLabel()))
				{		
					endGame(false);
				}
				else
				{
					newPuzzle.setLabel("Confirm");
					message = "Press 'Confirm' to get a new puzzle.";
				}
			}
			else
			{
				message = "";
				newPuzzle.setLabel("Give Up");
				if (enterButton.clicked(x,y))
				{	
					processSubmitWord();
				}	
				else if (clearLetter.clicked(x, y))
				{
					puzzle.clearLetter();
				}
				else if (clearWord.clicked(x, y))
				{
					puzzle.clearWord();
				}
				else if (mixButton.clicked(x, y))
				{
					puzzle.mix();
				}
				else
				{
					puzzle.clicked(x, y);
				}
			}	
		}
		else
		{	
			if (enterButton.clicked(x,y))
			{
				setUpGame();
			}
		}
	}
	public void processSubmitWord()
	{
		Word w = puzzle.evaluateWord();
		if (w.getPoints() == -2)
		{
			message = w.getWord() + " has already been used.";
		}
		else if (w.getPoints() == -1)
		{
			message = "Word must have at least 3 letters.";
		}
		else if (w.getPoints() == 0)
		{
			message = "Sorry, we don't have " + w.getWord() + " in our word list!";
			try
			{
				URL url = new URL(this.getParameter("site") + "Words/log.php?entry=" + w.getWord() + "&source=WDM&name=" + encName);
				this.getAppletContext().showDocument(url,"dummy");
			}
			catch(Exception ex)
			{
				log("WordMania Error 2: " + ex);
			}
		}
		else
		{
			message = w.getWord() + " - " + w.getPoints() + " points.";
			if (puzzle.allWordsFound())
			{
				repaint();
				endGame(true);
			}
		}	
	}
	public void paint(Graphics g)
	{
		og.setFont(new Font("Verdana",Font.PLAIN,20));
		if (gameStage > NOTSTARTED) 
		{
			og.setColor(new Color(139,0,60));
			og.drawString(message, 10, 30);
			og.setColor(new Color(0,125,0));
			og.drawString("Time: ", 10, 60);
			og.drawString("Points: ", 210, 60);
			og.setColor(timeColor);
			og.drawString("" + secondsLeft, 100, 60);
			og.setColor(Color.black);
			og.drawString("" + puzzle.getPoints(), 300, 60);
			puzzle.draw(og, gameStage);
			mixButton.draw(og);
			clearLetter.draw(og);
			clearWord.draw(og);
			newPuzzle.draw(og);
		}
		else
		{
			og.setColor(Color.black);
			og.drawString(message, 20, 30);
			og.drawString("You may use the mouse or the keyboard in this game.", 20, 60);
			og.drawString("Click the Start Button to start.", 20, 90);
		}
		enterButton.draw(og);
		// status bar simulator
		og.setColor(Color.black);
		og.setFont(new Font("Verdana",Font.PLAIN,10));
		og.drawString("ToMar WordMania Version " + VERSION, 0, 575);
		g.drawImage(offscreenImg, 0, 0, this);
	}
		
	public void update(Graphics g)
	{
		og.setColor(ToMarUtils.toMarBackground);
		og.fillRect(0, 0, WIDTH, HEIGHT);
		paint(g);
	}

	public void mouseClicked(MouseEvent e)
	{
	}
	public void mouseEntered(MouseEvent e)
	{
	}
	public void mouseReleased(MouseEvent e)
	{
	}
	public void keyTyped(KeyEvent k)
	{
		newPuzzle.setLabel("Give Up");
		String key = ("" + k.getKeyChar()).toUpperCase();
		puzzle.keyTyped(key);
	}

	public void keyPressed(KeyEvent k)
	{
		newPuzzle.setLabel("Give Up");
		if (k.getKeyCode() == KeyEvent.VK_ENTER)
		{
			if (gameStage == GAMEINPROGRESS)
			{	
				processSubmitWord();
			}
			else
			{
				setUpGame();
			}
		}
		else if (k.getKeyCode() == KeyEvent.VK_BACK_SPACE)
		{
			puzzle.clearLetter();
		}
	}
	public void keyReleased(KeyEvent e)
	{
	}
	public void mouseExited(MouseEvent arg0)
	{
	}
}