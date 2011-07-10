Birthday Block for Moodle 2.0 (and beyond):

Suggestions for improvement are welcome on the tracker (tracker.moodle.org).  

The birthday block has three configurable options which the site administrator controls. First, the site admin chooses which user profile field to use (date/time field type). The option for choosing a region specific date format (ISO, USA, and EUR) has been removed since it is obsolete as the date values are now stored as unix timestamps in the database rather than as text fields. This will make data entry more reliable when users input the data as they do not have to worry about format. Second, the site admin can choose whether to show or hide the block if there are no birthdays. Third, the site admin can choose the number of days in the future to display upcoming birthdays. This block will alphabetically (lastname, firstname) display users who are celebrating a birthday (or anniversary) on a given day (namely, today). The block makes use of the selected user profile field.  

Thanks to the generosity of translators the birthday block is now available in English, French, German, Hungarian, Italian, Japanese, Spanish, and Swedish. Other languages are welcome, simply create an issue in the tracker and upload the file and then I can add it to CVS.

 If you have any questions about the Birthday block please send me (Anthony Borrow, S.J.) a message via Moodle.org. 

Installation:

01) Copy the files into the \blocks folder so that you have a \blocks\birthday folder. 
02) Go to the \admin page and allow the block to be installed
03) Go to Users, Accounts, User profile fields
04) Create a new profile field of type Date/Time 
05) Input the Shortname (for example, DOB, Name (for example, Birthday), and a description for the user profile field
06) I recommend that the field not be required, that it be locked, and visible to user
07) I have used the default category; however, you are free to organize these fields however you wish and it should not effect functionality of the block.
08) Click on the Save Changes button
09) In the Site Administration block, click on Plugins, Blocks, and the settings link for the Birthday block
10) Select the user profile field you want the birthday block to use (only Date/Time user profile fields as shown). 
11) Enter your data (i.e. the dates into the created user profile field). If you plan to import the data via a csv upload, remember that the data is stored as a unix timestamp
13) Add the Birthday block to your page to see a list of those celebrating birthdays or anniversaries. If you want to customize or change the greetings, for example to Happy Anniversary! instead of Happy Birthday! simply edit the language file. Similarly, if you want the date displayed in a different format you can edit the birthday block's 'strftimedate'.  

Peace - Anthony
